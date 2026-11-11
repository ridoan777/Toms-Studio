<?php

namespace App\Http\Controllers\Workflows\Lessons;

use App\Domains\Lessons\Actions\NoteSaveAction;
use App\Domains\Lessons\Events\NoteNotifyEvent;
use App\Domains\Lessons\Services\GetLessonNoteActorsService;
use App\Enums\{CRUDActionEnums, NotifyChannelEnums};
use App\Helpers\{ApiJsonReturnHelper, UidGenerator};
use App\Helpers\Auth\OwnershipAuthCheck;
use App\Helpers\FileHelpers\{FileManagement, GalleryManagement};
use App\Http\Controllers\Controller;
use App\Helpers\Errors\ExceptionHandling;
use App\Http\Requests\Workflows\NoteStoreRequest;
use App\Models\Workflows\Lessons\{GroupStudent, Lesson, LessonGroup, LessonNote, LessonNoteGallery};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Support\Facades\{DB, Storage};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Throwable;


class ApiLessonNoteController extends Controller implements HasMiddleware
{
   use AuthorizesRequests;
   public static function middleware(): array
   {
      return [
         new Middleware(PermissionMiddleware::using('notes_index'), only: ['myNotes']),
         new Middleware(PermissionMiddleware::using('notes_show'), only: ['show']),
         new Middleware(PermissionMiddleware::using('notes_delete'), only: ['delete']),
      ];
   }
   // -----------------------------------------------

   public function myNotes(Request $request)
   {
      try {
         $USER = $request->user();

         $notes = LessonNote::with([
            'noteRelatingBackTo_Lesson.lessonRelatingBackTo_Teacher:id,user_uid,username,name,user_role,avatar,status',
            // 'noteRelatingBackTo_Lesson.lessonRelatingBackTo_Student:id,user_uid,username,name,user_role,avatar,status',
            // 'noteRelatingBackTo_Group.groupRelatingBackTo_Teacher:id,user_uid,username,name,user_role,avatar,status',
            'noteRelatingBackTo_Group.groupRelationWith_Students.studentRelatingBackTo_User:id,user_uid,username,name,user_role,avatar,status',
            'noteRelatingBackTo_Student:id,user_uid,username,name,user_role,avatar,status',
            'noteRelationWith_Gallery:note_id,filename,filepath,created_at'
         ]);

         if ($USER->hasRoleKey('student')) {
            $notes->where(function ($q) use ($USER) {
               $q->where('student_id', $USER->id);
            });
         } else {
            $notes->where(function ($q) use ($USER) {
               $q->whereHas('noteRelatingBackTo_Lesson', function ($lesson) use ($USER) {
                  $lesson->where('teacher_id', $USER->id);
               })
                  ->orWhereHas('noteRelatingBackTo_Group', function ($group) use ($USER) {
                     $group->where('teacher_id', $USER->id);
                  });
            });
         }

         $notes = $notes->latest()->paginate(20);

         $notes->getCollection()->transform(function ($note) {
            return $this->transformNote($note);
         });

         return ApiJsonReturnHelper::handle(true, 200, "My lesson notes have been fetched successfully!", $notes);

      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching my lesson notes', $e);
      }
   }
   // -----------------------------------------------

   public function show(Request $request, LessonNote $note)
   {
      try {
         $USER = $request->user();

         $allowedIds = [
            (int)$note->student_id,
            (int)$note->creator_id,
            (int)($note->noteRelatingBackTo_Lesson?->teacher_id),
            (int)($note->noteRelatingBackTo_Group?->teacher_id)
         ];

         OwnershipAuthCheck::ownerInList($allowedIds, $USER->id, 'lesson note');

         $note->load([
            'noteRelationWith_Gallery',
            'noteRelatingBackTo_Lesson.lessonRelatingBackTo_Teacher:id,user_uid,username,name,user_role,avatar,status',
            'noteRelatingBackTo_Lesson.lessonRelatingBackTo_Student:id,user_uid,username,name,user_role,avatar,status',
            'noteRelatingBackTo_Lesson.lessonRelatingBackTo_Intrument:id,name,status',
            'noteRelatingBackTo_Group.groupRelatingBackTo_Teacher:id,user_uid,username,name,user_role,avatar,status',
            'noteRelatingBackTo_Group.groupRelatingBackTo_Intrument:id,name,status',
            'noteRelatingBackTo_Student:id,user_uid,username,name,user_role,avatar,status',
         ]);

         $transformedNote = $this->transformNote($note);

         return ApiJsonReturnHelper::handle(true, 200, "A note has been fetched successfully!", $transformedNote);

      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching a note', $e);
      }
   }
   // -----------------------------------------------

   public function store(NoteStoreRequest $request, NoteSaveAction $action, GetLessonNoteActorsService $targetResolver)
   {
      $uploadedPaths = [];
      try {
         $validated = $request->validated();
         $USER = $request->user();
         $existingNote = null;
         $isNewRecord = false;

         if (isset($validated['lesson_id']) && isset($validated['group_id'])) {
            ExceptionHandling::bailout(409, "You cannot add a note for a lesson & a group lesson at a time !");
         }

         $GetTargetNoteActors = $targetResolver->handle($validated);
         $targetResolver->checkStudentEnrollment($GetTargetNoteActors);

         // ------------ CHECKING EXISTANCE + UID ------------
         if (isset($validated['id']) && $validated['id']) {
            $this->authorize('notes_update');
            $existingNote = LessonNote::find($validated['id']);
            $NOTE_UID = $existingNote->note_uid;
         } else {
            $this->authorize('notes_create');
            $NOTE_UID = UidGenerator::uniqueULID($validated['title'], 12, 18, 4);
            $isNewRecord = true;
         }
         // ------------ CHECKING EXISTANCE + UID ------------

         $result = DB::transaction(function () use ($validated, $existingNote, $NOTE_UID, $USER, $isNewRecord, $action, &$uploadedPaths) {
            $this->authorize('notes_file_edit');

            $note = $action->execute($validated, $existingNote, $NOTE_UID, $USER, $isNewRecord);

            $IMAGE_FOLDER = 'Lesson_notes/' . $note->note_uid;
            $FILE_NAME = "NOTE-" . ($note?->note_uid);

            $videoGallery = GalleryManagement::handle($validated['video'] ?? [], LessonNoteGallery::class, $note, 'note_id', $IMAGE_FOLDER, $FILE_NAME, true, $uploadedPaths);
            $audioGallery = GalleryManagement::handle($validated['audio'] ?? [], LessonNoteGallery::class, $note, 'note_id', $IMAGE_FOLDER, $FILE_NAME, true, $uploadedPaths);
            $docGallery = GalleryManagement::handle($validated['doc'] ?? [], LessonNoteGallery::class, $note, 'note_id', $IMAGE_FOLDER, $FILE_NAME, true, $uploadedPaths);

            return [
               'note' => $note,
               'video_gallery' => $videoGallery,
               'audio_gallery' => $audioGallery,
               'doc_gallery' => $docGallery,
            ];

         });

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = $isNewRecord ? CRUDActionEnums::CREATE : CRUDActionEnums::UPDATE;

         $targetUsers = array_values(array_filter(array_unique([$GetTargetNoteActors['teacher_id'], $GetTargetNoteActors['student_id']])));

         $targetUsers = array_values(array_diff($targetUsers, [$USER->id]));
         $noteName = str($result['note']?->title)->limit(30, '...');

         event(new NoteNotifyEvent($result['note'], $noteName, $USER, $actionFlag, [NotifyChannelEnums::PUSH, NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return ApiJsonReturnHelper::handle(true, 200, "A lesson note has been saved successfully!", $result);
      } catch (Throwable $e) {
         foreach ($uploadedPaths as $path) {
            if ($path) {
               Storage::disk(config('filesystems.default'))->delete($path);
            }
         }
         return ExceptionHandling::handle('saving lesson note', $e);
      }
   }
   // -----------------------------------------------

   public function delete(Request $request, LessonNote $note)
   {
      $name = null;
      $imageFolder = null;
      try {
         $name = $note?->title ?? null;
         $DISK_FOLDER = config('filesystems.default');

         OwnershipAuthCheck::ownerInList([$note?->creator_id, $note?->noteRelatingBackTo_Lesson?->teacher_id, $note?->noteRelatingBackTo_Group?->teacher_id], $request?->user()?->id, "delete feature");

         $firstGallery = LessonNoteGallery::note($note->id)->first();
         $trackFile = $firstGallery?->filepath ?? null;
         $imageFolder = dirname($trackFile);

         if ($imageFolder) {
            FileManagement::deleteFolder($imageFolder, $DISK_FOLDER);
         }

         $note->delete();

         return ApiJsonReturnHelper::handle(true, 200, "Note {$name} has been deleted successfully!", null);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('deleting a note', $e);
      }
   }
   // -----------------------------------------------

   private function transformNote($note)
   {
      $userHidden = ['created_by', 'phone', 'email', 'email_verified_at', 'guardian', 'position', 'address', 'timezone', 'fcm_token', 'created_at', 'updated_at', 'stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at'];

      $lessonHidden = ['difficulty', 'remarks', 'creator', 'last_modifier', 'status', 'created_at', 'updated_at'];

      // ---------------- STUDENT ----------------
      $note->student_details = $note->noteRelatingBackTo_Student
         ? $note->noteRelatingBackTo_Student->makeHidden($userHidden)->toArray() : null;

      // ---------------- LESSON ----------------
      if ($note->noteRelatingBackTo_Lesson) {

         $lesson = $note->noteRelatingBackTo_Lesson?->makeHidden($lessonHidden);

         $lesson->teacher_details = $lesson->lessonRelatingBackTo_Teacher?->makeHidden($userHidden)?->toArray();

         $lesson->instrument_details = $lesson->lessonRelatingBackTo_Intrument;

         $lesson->unsetRelation('lessonRelatingBackTo_Teacher');
         $lesson->unsetRelation('lessonRelatingBackTo_Student');
         $lesson->unsetRelation('lessonRelatingBackTo_Intrument');

         $note->lesson_details = $lesson;

      } else {
         $note->lesson_details = null;
      }

      // ---------------- GROUP ----------------
      if ($note->noteRelatingBackTo_Group) {

         $group = $note->noteRelatingBackTo_Group?->makeHidden($lessonHidden);

         $group->teacher_details = $group->groupRelatingBackTo_Teacher?->makeHidden($userHidden)?->toArray();

         $group->instrument_details = $group->groupRelatingBackTo_Intrument;

         $group->unsetRelation('groupRelatingBackTo_Teacher');
         $group->unsetRelation('groupRelatingBackTo_Intrument');
         $group->unsetRelation('groupRelationWith_Students');

         $note->group_details = $group;

      } else {
         $note->group_details = null;
      }

      // ---------------- GALLERY ----------------
      $note->gallery_details = $note->noteRelationWith_Gallery;
      $note->unsetRelation('noteRelationWith_Gallery');

      // ---------------- CLEAN ORIGINAL RELATIONS ----------------
      $note->unsetRelation('noteRelatingBackTo_Lesson');
      $note->unsetRelation('noteRelatingBackTo_Group');
      $note->unsetRelation('noteRelatingBackTo_Student');

      return $note;
   }
}