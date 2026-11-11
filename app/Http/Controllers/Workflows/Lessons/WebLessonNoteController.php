<?php

namespace App\Http\Controllers\Workflows\Lessons;

use App\Domains\Lessons\Actions\NoteSaveAction;
use App\Domains\Lessons\Events\NoteNotifyEvent;
use App\Domains\Lessons\Services\GetLessonNoteActorsService;
use App\Enums\{CRUDActionEnums, NotifyChannelEnums};
use App\Helpers\Errors\ExceptionHandling;
use App\Helpers\FileHelpers\{FileManagement, GalleryManagement};
use App\Helpers\Ui\DatatableHelper;
use App\Helpers\UidGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflows\NoteStoreRequest;
use App\Models\Workflows\Lessons\{GroupStudent, Lesson, LessonGroup, LessonNote, LessonNoteGallery};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Support\Facades\{DB, Log};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Throwable;


// class ApiLessonController extends Controller
class WebLessonNoteController extends Controller implements HasMiddleware
{
   use AuthorizesRequests;
   public static function middleware(): array
   {
      return [
         new Middleware(PermissionMiddleware::using('lesson_index'), only: ['index', 'myLessons']),
         new Middleware(PermissionMiddleware::using('lesson_show'), only: ['showSingle', 'showGroup']),
      ];
   }
   // -----------------------------------------------

   public function create()
   {
      $lessons = Lesson::with('lessonRelatingBackTo_Intrument:id,name')->status(1)->get();
      $lessonGroups = LessonGroup::with('groupRelatingBackTo_Intrument:id,name')->status(1)->get();

      return view('Admin.sidebar.Lesson_notes.create', compact('lessons', 'lessonGroups'));
   }

   public function getStudents(Request $request)
   {
      if ($request->filled('lesson_id')) {
         $students = Lesson::status(1)->where('id', $request->lesson_id)->whereHas('lessonRelatingBackTo_Student', fn($q) => $q->status(1))->with('lessonRelatingBackTo_Student:id,user_uid,username,name,user_role,address,status')->get()->pluck('lessonRelatingBackTo_Student')->filter()->unique('id')->values();
      } elseif ($request->filled('group_id')) {
         $students = GroupStudent::status(1)->where('group_id', $request->group_id)->whereHas('studentRelatingBackTo_User', fn($q) => $q->status(1))->with('studentRelatingBackTo_User:id,user_uid,username,name,user_role,address,status')->get()->pluck('studentRelatingBackTo_User')->filter()->unique('id')->values();
      } else {
         $students = collect();
      }

      return response()->json($students->map(fn($student) => ['id' => $student->id, 'text' => "[{$student->id}] | {$student->name} | {$student->address}"]));
   }


   public function index()
   {
      return view('Admin.sidebar.Lesson_notes.index');
   }


   public function show(LessonNote $note)
   {
      try {
         $lessonNote = $note->load(['noteRelationWith_Gallery', 'noteRelatingBackTo_Creator:id,user_uid,username,name,user_role,email,avatar', 'noteRelatingBackTo_LastModifier:id,user_uid,username,name,user_role,email,avatar']);

         $lessons = Lesson::with('lessonRelatingBackTo_Intrument:id,name')->status(1)->get();
         $lessonGroups = LessonGroup::with('groupRelatingBackTo_Intrument:id,name')->status(1)->get();

         $singleLessonStudents = Lesson::status(1)->whereHas('lessonRelatingBackTo_Student', fn($q) => $q->status(1))
            ->with('lessonRelatingBackTo_Student:id,user_uid,username,name,user_role,address,status')
            ->get()->pluck('lessonRelatingBackTo_Student');

         $groupLessonStudents = GroupStudent::status(1)->whereHas('studentRelatingBackTo_User', fn($q) => $q->status(1))
            ->with('studentRelatingBackTo_User:id,user_uid,username,name,user_role,address,status')->get()->pluck('studentRelatingBackTo_User');

         $students = collect()->merge($singleLessonStudents)->merge($groupLessonStudents)->filter()->unique('id')->sortBy('name')->values();

         return view('Admin.sidebar.Lesson_notes.show', compact('lessonNote', 'lessons', 'lessonGroups', 'students'));
      } catch (Exception $e) {
         return response()->json(['success' => false, 'error' => $e->getMessage()]);
      }
   }


   public function store(NoteStoreRequest $request, NoteSaveAction $action, GetLessonNoteActorsService $targetResolver)
   {
      $uploadedPaths = [];
      try {
         $validated = $request->validated();
         // dd($validated);
         $USER = $request->user();
         $existingNote = null;
         $isNewRecord = false;

         if (!empty($validated['lesson_id']) && !empty($validated['group_id'])) {
            ExceptionHandling::bailout(409, "You cannot add a note for a lesson & a group lesson at a time!");
         }

         $GetTargetNoteActors = $targetResolver->handle($validated);
         $targetResolver->checkStudentEnrollment($GetTargetNoteActors);

         $students = $validated['student_id'] ?? [];
         $savedNotes = [];

         foreach ($students as $currentStudent) {
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

            $result = DB::transaction(function () use ($validated, $currentStudent, $existingNote, $NOTE_UID, $USER, $isNewRecord, $action, &$uploadedPaths) {
               $this->authorize('notes_file_edit');

               $note = $action->execute($validated, $currentStudent, $existingNote, $NOTE_UID, $USER, $isNewRecord);

               $IMAGE_FOLDER = 'Lesson_notes/' . $note->note_uid;
               $FILE_NAME = "NOTE-GALLERY-" . ($note?->note_uid);

               GalleryManagement::handle($validated['video'] ?? [], LessonNoteGallery::class, $note, 'note_id', $IMAGE_FOLDER, $FILE_NAME, true, $uploadedPaths);
               GalleryManagement::handle($validated['audio'] ?? [], LessonNoteGallery::class, $note, 'note_id', $IMAGE_FOLDER, $FILE_NAME, true, $uploadedPaths);
               GalleryManagement::handle($validated['doc'] ?? [], LessonNoteGallery::class, $note, 'note_id', $IMAGE_FOLDER, $FILE_NAME, true, $uploadedPaths);

               return ['note' => $note];
            });

            $savedNotes[$currentStudent] = $result['note'];
         }

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = empty($validated['id']) ? CRUDActionEnums::CREATE : CRUDActionEnums::UPDATE;

         foreach ($savedNotes as $studentId => $note) {
            $targetUsers = array_values(array_filter(array_unique([$GetTargetNoteActors['teacher_id'], $studentId])));
            $targetUsers = array_values(array_diff($targetUsers, [$USER->id]));
            $noteName = str($note?->title)->limit(30, '...');

            event(new NoteNotifyEvent($note, $noteName, $USER, $actionFlag, [NotifyChannelEnums::PUSH, NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], $targetUsers));
         }
         // -------------------- NOTIFICATION EVENTS --------------------

         return redirect()->back()->with('success', "Lesson note '{$validated['title']}' is saved successfully.");
      } catch (Throwable $e) {
         return back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
      }
   }


   public function toggle(Request $request, LessonNote $note, GetLessonNoteActorsService $targetResolver)
   {
      try {
         $note->update([
            'status' => !$note->status,
         ]);

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = $note->status ? CRUDActionEnums::ACTIVATE : CRUDActionEnums::DEACTIVATE;

         $GetTargetNoteActors = $targetResolver->handle(null, $note);
         $targetUsers = array_filter([$GetTargetNoteActors['teacher_id'], $GetTargetNoteActors['student_id']]);

         event(new NoteNotifyEvent($note, str($note?->title, 30, '...'), $request->user(), $actionFlag, [NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return response()->json(['success' => true]);
      } catch (Exception $e) {
         return response()->json(['success' => false, 'error' => $e->getMessage()]);
      }
   }


   public function delete(Request $request, LessonNote $note, GetLessonNoteActorsService $targetResolver)
   {
      $name = null;
      try {
         $USER = $request->user();
         $DISK_FOLDER = config('filesystems.default');

         $name = $note?->title ?? null;
         $GetTargetNoteActors = $targetResolver->handle(null, $note);
         $targetUsers = array_filter([$GetTargetNoteActors['teacher_id'], $GetTargetNoteActors['student_id']]);

         $firstGallery = LessonNoteGallery::note($note->id)->first();
         $trackFile = $firstGallery->filepath ?? null;
         $imageFolder = dirname($trackFile);

         $note->delete();

         if ($imageFolder) {
            FileManagement::deleteFolder($imageFolder, $DISK_FOLDER);
         }

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = CRUDActionEnums::DELETE;

         event(new NoteNotifyEvent(null, $name, $USER, $actionFlag, [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return redirect()->route('backend_lesson_note_index')->with('success', "A note '{$name}' has been deleted successfully.");
      } catch (Exception $e) {
         return redirect()->back()->with('error', 'Action Error: ' . $e->getMessage())->withInput();
      }
   }


   public function bulkDelete(Request $request, GetLessonNoteActorsService $targetResolver)
   {
      $request->validate([
         'ids' => 'required|array',
         'ids.*' => 'integer|exists:lesson_notes,id',
      ]);

      $USER = $request->user();
      $ids = $request->ids;

      /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Workflows\Lessons\LessonNote> $lessonNotes */

      $lessonNotes = LessonNote::whereIn('id', $ids)->get();

      if ($lessonNotes->isEmpty()) {
         return back()->with('error', 'No valid notes selected.');
      }

      $MESSAGE = "Selected notes are deleted.";
      $DISK_FOLDER = config('filesystems.default');
      $SKIPPED = [];
      $deleted = [];

      DB::beginTransaction();

      try {
         foreach ($lessonNotes as $index => $note) {
            /** @var \App\Models\Workflows\Lessons\LessonNote $note */

            $name = $note?->title;
            $firstGallery = LessonNoteGallery::note($note->id)->first();

            $trackFile = $firstGallery->filepath ?? null;
            $imageFolder = dirname($trackFile);

            $GetTargetNoteActors = $targetResolver->handle(null, $note);
            $targetUsers = array_filter([$GetTargetNoteActors['teacher_id'], $GetTargetNoteActors['student_id']]);

            $deleted[] = $note?->title ?? "note-" . $index;

            $note->delete();

            if ($imageFolder) {
               FileManagement::deleteFolder($imageFolder, $DISK_FOLDER);
            }

            // -------------------- NOTIFICATION EVENTS --------------------
            $actionFlag = CRUDActionEnums::DELETE;
            event(new NoteNotifyEvent(null, $name, $USER, $actionFlag, [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], $targetUsers));
            // -------------------- NOTIFICATION EVENTS --------------------
         }

         DB::commit();

         $MESSAGE = $MESSAGE . "success: [" . count($deleted) . "], dispute-skipped: [" . count($SKIPPED) . "].";

         return redirect()->back()->with('success', $MESSAGE);
      } catch (Throwable $e) {
         DB::rollBack();

         Log::error('Notes bulk delete failed', ['ids' => $ids, 'error' => $e->getMessage()]);
         return back()->with('error', 'Bulk delete failed. No changes were made.');
      }
   }


   public function lessonNoteDatatable(Request $request)
   {
      try {
         $columns = ['id', 'id', 'id', 'note_uid', 'title', 'note', 'lesson_id', 'group_id', 'gallery_id', 'id', 'student_id', 'status', 'created_at', 'id'];

         $draw = (int)$request->input('draw');
         $start = (int)$request->input('start', 0);
         $length = (int)$request->input('length', 25);
         $orderColIndex = (int)$request->input('order.0.column', 0);
         $orderCol = $columns[$orderColIndex] ?? 'id';
         $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

         $query = LessonNote::with([
            'noteRelatingBackTo_Lesson.lessonRelatingBackTo_Teacher',
            'noteRelatingBackTo_Lesson.lessonRelatingBackTo_Student',
            'noteRelatingBackTo_Lesson.lessonRelatingBackTo_Intrument',

            'noteRelatingBackTo_Group.groupRelatingBackTo_Teacher',
            'noteRelatingBackTo_Group.groupRelatingBackTo_Intrument',

            'noteRelatingBackTo_Student',
            'noteRelationWith_Gallery',
         ])
            ->select(['id', 'note_uid', 'lesson_id', 'group_id', 'student_id', 'title', 'note', 'creator_id', 'last_modifier_id', 'status', 'created_at']);

         if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
               foreach (['note_uid', 'title', 'note'] as $field) {
                  $q->orWhere($field, 'like', "%{$search}%");
               }
               $q->orWhereHas('noteRelatingBackTo_Lesson', function ($q) use ($search) {
                  $q->where(function ($sub) use ($search) {
                     foreach (['lesson_uid', 'title', 'schedule_day'] as $field) {
                        $sub->orWhere($field, 'like', "%{$search}%");
                     }
                  });
               });
               $q->orWhereHas('noteRelatingBackTo_Group', function ($q) use ($search) {
                  $q->where(function ($sub) use ($search) {
                     foreach (['group_uid', 'title', 'schedule_day'] as $field) {
                        $sub->orWhere($field, 'like', "%{$search}%");
                     }
                  });
               });
               foreach ([
                  'noteRelatingBackTo_Student',
                  'noteRelatingBackTo_Lesson.lessonRelatingBackTo_Teacher',
                  'noteRelatingBackTo_Lesson.lessonRelatingBackTo_Student',
                  'noteRelatingBackTo_Group.groupRelatingBackTo_Teacher',
               ] as $relation) {
                  $q->orWhereHas($relation, function ($q) use ($search) {
                     $q->where(function ($sub) use ($search) {
                        foreach (['user_uid', 'username', 'name', 'email', 'phone'] as $field) {
                           $sub->orWhere($field, 'like', "%{$search}%");
                        }
                     });
                  });
               }

               $q->orWhereHas('noteRelatingBackTo_Lesson.lessonRelatingBackTo_Intrument', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
               });

               $q->orWhereHas('noteRelatingBackTo_Group.groupRelatingBackTo_Intrument', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
               });
            });
         }

         $recordsTotal = LessonNote::count();
         $recordsFiltered = $query->count();

         $data = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

         $sn = $start;
         $zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();

         $data->transform(function ($row) use (&$sn, $zones) {

            $row->DT_RowId = 'row_' . $row->id;
            $row->view_url = route('backend_lesson_note_show', $row->id);

            $row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';
            $row->SN = ++$sn;

            $row->note_uid = '<span class="text-sm text-gray-600">' . $row->note_uid . '</span>';

            $row->note_short = '<span class="text-xs text-gray-600">' . e(\Illuminate\Support\Str::limit(strip_tags($row->note), 80)) . '</span>';

            $lesson = $row->noteRelatingBackTo_Lesson;
            $group = $row->noteRelatingBackTo_Group;

            $row->lesson_details = $lesson ? $row->detail = DatatableHelper::detailBlock($lesson, true, $lesson?->lesson_uid, false, null) : "N/A";
            $row->group_details = $group ? $row->detail = DatatableHelper::detailBlock($group, true, $group?->group_uid, false, null, "Instrument", $group?->groupRelatingBackTo_Intrument?->name) : "N/A";

            $galleryCount = $row->noteRelationWith_Gallery?->count() ?? 0;

            $row->resources = '<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">' . $galleryCount . ' File(s)</span>';

            $teacher = $lesson?->lessonRelatingBackTo_Teacher ?? $group?->groupRelatingBackTo_Teacher;
            $row->teacher = DatatableHelper::userBlock($teacher);

            $row->student = DatatableHelper::userBlock($row->noteRelatingBackTo_Student);

            $row->status_label = DatatableHelper::phaseColor($row->status);

            $row->created_at_formatted = $row->created_at
               ? Carbon::parse($row->created_at, 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')'
               : 'N/A';

            $row->actions = DatatableHelper::datatableAction($row, true, 'backend_lesson_note_toggle', 'backend_lesson_note_delete');

            return $row;
         });

         return DatatableHelper::handleDatatableSuccess($draw, $recordsTotal, $recordsFiltered, $data);

      } catch (Exception $e) {
         return DatatableHelper::handleDatatableError($request, $e, true, "Lesson Note Datatable Error");
      }
   }
}