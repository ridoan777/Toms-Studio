<?php

namespace App\Http\Controllers\Workflows\Lessons;

use App\Domains\Lessons\Actions\HistorySaveAction;
use App\Domains\Lessons\Enums\LessonPhaseEnums;
use App\Domains\Lessons\Events\HistoryNotifyEvent;
use App\Enums\{CRUDActionEnums, NotifyChannelEnums};
use App\Helpers\{ApiJsonReturnHelper, Auth\OwnershipAuthCheck, Errors\ExceptionHandling, Ui\DatatableHelper, UidGenerator};
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflows\LessonHistoryStoreRequest;
use App\Models\Users\User;
use App\Models\Workflows\Lessons\{Lesson, LessonGroup, LessonHistory};
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class ApiLessonHistoryController extends Controller implements HasMiddleware
{
   public static function middleware(): array
   {
      return [
         new Middleware('verify_role_key:admin,teacher', only: ['viewTeacher', 'delete']),
         new Middleware('verify_role_key:admin,student', only: ['viewStudent']),
      ];
   }
   // -----------------------------------------------

   public function viewTeacher(Request $request)
   {
      try {
         $USER = $request->user();
         $page = $request->input('page_no', 1);
         $perPage = $request->input('per_page_items', 2);

         $allHistories = LessonHistory::with([
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Teacher:id,name,user_role,avatar',
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Student:id,name,user_role,avatar',
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Intrument:id,name',
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Teacher:id,name,user_role,avatar',
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Creator:id,name,user_role,avatar',
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Intrument',
            'scheduleRelatingBackTo_LessonGroup.groupRelationWith_Students.studentRelatingBackTo_User:id,name,user_role,avatar',
            'scheduleRelatingBackTo_Creator:id,name,user_role,avatar',
            'scheduleRelatingBackTo_Modifier:id,name,user_role,avatar'
         ])
            ->where(function ($query) use ($USER) {
               $query->whereHas('scheduleRelatingBackTo_Lesson', fn($q) => $q->where('teacher_id', $USER->id))
                  ->orWhereHas('scheduleRelatingBackTo_LessonGroup', fn($q) => $q->where('teacher_id', $USER->id));
            })
            ->orderBy('final_date')
            ->get()
            ->transform(fn($item) => $this->transformHistory($item, false))
            ->groupBy(fn($item) => $item->computed_final_date ?? 'unknown')
            ->sortKeys();

         $allHistories->makeHidden(['created_at', 'updated_at']);

         $totalDates = $allHistories->count();
         $paginatedDates = $allHistories
            ->slice(($page - 1) * $perPage, $perPage)
            ->map(fn($items, $date) => ['date' => $date, 'items' => $items->values()])
            ->values();

         $pagination = ['current_page' => (int)$page, 'per_page' => (int)$perPage, 'total_dates' => $totalDates, 'last_page' => (int)ceil($totalDates / $perPage), 'has_more' => ($page * $perPage) < $totalDates];

         return ApiJsonReturnHelper::handle(true, 200, "All lesson histories have been fetched sucessfully!", ['pagination' => $pagination, 'data' => $paginatedDates]);

      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching lesson histories', $e);
      }
   }
   // -----------------------------------------------

   public function viewStudent(Request $request)
   {
      try {
         $USER = $request->user();
         $page = $request->input('page_no', 1);
         $perPage = $request->input('per_page_items', 2);

         $allHistories = LessonHistory::with([
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Teacher:id,name,user_role,avatar',
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Student:id,name,user_role,avatar',
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Intrument:id,name',
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Teacher:id,name,user_role,avatar',
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Creator:id,name,user_role,avatar',
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Intrument',
            'scheduleRelatingBackTo_LessonGroup.groupRelationWith_Students.studentRelatingBackTo_User:id,name,user_role,avatar',
            'scheduleRelatingBackTo_Creator:id,name,user_role,avatar',
            'scheduleRelatingBackTo_Modifier:id,name,user_role,avatar'
         ])
            ->where(function ($query) use ($USER) {
               $query->whereHas('scheduleRelatingBackTo_Lesson', function ($q) use ($USER) {
                  $q->where('student_id', $USER->id);
               })
                  ->orWhereHas('scheduleRelatingBackTo_LessonGroup', function ($q) use ($USER) {
                     $q->whereHas('groupRelationWith_Students', function ($q2) use ($USER) {
                        $q2->where('student_id', $USER->id);
                     });
                  });
            })
            ->orderBy('final_date')
            ->get()
            ->transform(fn($item) => $this->transformHistory($item, false))
            ->groupBy(fn($item) => $item->computed_final_date ?? 'unknown')
            ->sortKeys();

         $allHistories->makeHidden(['created_at', 'updated_at']);

         $totalDates = $allHistories->count();
         $paginatedDates = $allHistories
            ->slice(($page - 1) * $perPage, $perPage)
            ->map(fn($items, $date) => ['date' => $date, 'items' => $items->values()])
            ->values();

         $pagination = ['current_page' => (int)$page, 'per_page' => (int)$perPage, 'total_dates' => $totalDates, 'last_page' => (int)ceil($totalDates / $perPage), 'has_more' => ($page * $perPage) < $totalDates];

         return ApiJsonReturnHelper::handle(true, 200, "All lesson histories have been fetched sucessfully!", ['pagination' => $pagination, 'data' => $paginatedDates]);

      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching lesson histories', $e);
      }
   }
   // -----------------------------------------------

   public function show(Request $request, LessonHistory $history)
   {
      try {
         $USER = $request->user();

         $history->load($history->lesson_id ? [
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Teacher:id,user_uid,username,name,user_role,avatar,status',
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Student:id,user_uid,username,name,user_role,avatar,status',
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Intrument:id,name',
            'scheduleRelatingBackTo_Creator:id,user_uid,username,name,user_role,avatar,status',
            'scheduleRelatingBackTo_Modifier:id,user_uid,username,name,user_role,avatar,status'
         ] : [
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Teacher:id,user_uid,username,name,user_role,avatar,status',
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Creator:id,user_uid,username,name,user_role,avatar,status',
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Intrument:id,name,note,status',
            'scheduleRelatingBackTo_LessonGroup.groupRelationWith_Students.studentRelatingBackTo_User:id,user_uid,username,name,user_role,avatar,status',
            'scheduleRelatingBackTo_Creator:id,user_uid,username,name,user_role,avatar,status',
            'scheduleRelatingBackTo_Modifier:id,user_uid,username,name,user_role,avatar,status'
         ]);

         $source = $history->scheduleRelatingBackTo_Lesson ?? $history->scheduleRelatingBackTo_LessonGroup;
         OwnershipAuthCheck::ownerVsOwner($source->teacher_id, $USER->id);

         $history = $this->transformHistory($history);
         return ApiJsonReturnHelper::handle(true, 200, "A class schedule history has been fetched successfully!", $history);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching a schedule history', $e);
      }
   }
   // -----------------------------------------------

   public function store(LessonHistoryStoreRequest $request, HistorySaveAction $action)
   {
      try {
         $type = $request->input('type');
         $validated = $request->validated();
         $USER = $request->user();
         $LESSON_HISTORY_UID = null;
         $existingHistory = null;
         $isNewRecord = false;
         // dd($validated);

         $class = null;
         if ($type === 'lesson') {
            $class = Lesson::findOrFail((int)$validated['lesson_id']);
         } else {
            $class = LessonGroup::findOrFail((int)$validated['group_id']);
         }

         // -------------------- AUTHORIZATED USERS --------------------
         $teacher = $class->teacher_id;
         $studentIds = $type === 'group'
            ? $class?->groupRelationWith_Students?->pluck('student_id')?->filter()?->map(fn($id) => (int)$id)?->toArray() ?? []
            : collect([$class?->student_id])->filter()->map(fn($id) => (int)$id)->toArray();

         if ($USER->hasRoleKey('teacher')) {
            OwnershipAuthCheck::ownerVsOwner($class->teacher_id, $USER->id, "history");
         } else if ($USER->hasRoleKey('student')) {
            if($type === 'group')
               ExceptionHandling::bailout(409, "You cannot modify a group lesson history!");

            if (($validated['phase'] ?? null) !== LessonPhaseEnums::CANCELLED->value) {
               ExceptionHandling::bailout(409, "Students can only request cancellation for private lessons!");
            }
         } else {
            OwnershipAuthCheck::ownerInList($studentIds, $USER->id, "history");
         }
         // -------------------- AUTHORIZATED USERS --------------------

         if (isset($validated['final_date']) && $class->starting_date && Carbon::parse($validated['final_date'])->isBefore($class->starting_date)) {
            ExceptionHandling::bailout(422, "The final date cannot be earlier than the lesson's starting date (" . Carbon::parse($class->starting_date)->format('Y-m-d') . ").");
         }

         // ------------ CHECKING EXISTANCE + UID ------------
         if (isset($validated['id']) && $validated['id']) {
            $existingHistory = LessonHistory::findOrFail($validated['id']);
            $LESSON_HISTORY_UID = $existingHistory->lesson_history_uid;
         } else {
            $LESSON_HISTORY_UID = UidGenerator::uniqueULID("HIST-" . strtoupper($type), 12, 18, 4);
            $isNewRecord = true;
         }
         // ------------ CHECKING EXISTANCE + UID ------------

         $history = $action->execute($validated, $existingHistory, $type, $class, $USER, $LESSON_HISTORY_UID, $isNewRecord);

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = $isNewRecord ? CRUDActionEnums::CREATE : CRUDActionEnums::UPDATE;
         $targetUsers = array_values(array_unique(array_filter(array_merge([(int)$teacher], $studentIds))));

         event(new HistoryNotifyEvent(($history), null, $USER, $actionFlag, [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         if ($type === 'group') {
            $history->group_lesson_details = $history->scheduleRelatingBackTo_LessonGroup;
            $history->unsetRelation('scheduleRelatingBackTo_LessonGroup');
            $history->unsetRelation('scheduleRelatingBackTo_Lesson');
         } else {
            $history->lesson_details = $history->scheduleRelatingBackTo_Lesson;
            $history->unsetRelation('scheduleRelatingBackTo_Lesson');
            $history->unsetRelation('scheduleRelatingBackTo_LessonGroup');
         }

         return ApiJsonReturnHelper::handle(true, 200, "A lesson schedule has been saved sucessfully!", $history);

      } catch (Throwable $e) {
         return ExceptionHandling::handle('saving a lesson schedule', $e);
      }
   }

   // -----------------------------------------------

   public function delete(Request $request, LessonHistory $history)
   {
      $name = null;
      try {
         $USER = $request->user();
         $lesson = $history?->scheduleRelatingBackTo_Lesson ?? null;
         $group = $history?->scheduleRelatingBackTo_LessonGroup ?? null;
         $teacher = $lesson ? $lesson->teacher_id : ($group ? $group->teacher_id : null);

         $studentIds = $group
            ? $group?->groupRelationWith_Students?->pluck('student_id')?->filter()?->map(fn($id) => (int)$id)?->toArray() ?? []
            : collect([$lesson?->student_id])->filter()->map(fn($id) => (int)$id)->toArray();

         $name = $lesson ? $lesson->title : ($group->title ?? null);
         if (!$USER->hasRoleKey('admin', 'super_admin')) {
            OwnershipAuthCheck::ownerVsOwner($history->creator, $USER->id);
         }
         $history->delete();

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = CRUDActionEnums::DELETE;
         $targetUsers = array_values(array_unique(array_filter(array_merge([(int)$teacher], $studentIds))));

         event(new HistoryNotifyEvent($history, null, $USER, $actionFlag, [NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return ApiJsonReturnHelper::handle(true, 200, "A class schedule for {$name} is deleted sucessfully!");
      } catch (Throwable $e) {
         return ExceptionHandling::handle('deleting a class schedule', $e);
      }
   }

   // ----------------------- HELPERS -----------------------

   private function transformHistory($history, ?bool $showStudents = true)
   {
      $source = $history->scheduleRelatingBackTo_Lesson ?? $history->scheduleRelatingBackTo_LessonGroup;

      if (!$source) {
         $history->computed_final_date = 'unknown';
         return $history;
      }

      $history->type = $history->scheduleRelatingBackTo_Lesson ? 'single' : 'group';
      $history->computed_final_date = $history->getRawOriginal('final_date') ? \Carbon\Carbon::parse($history->getRawOriginal('final_date'))->format('Y-m-d') : 'unknown'; // don't change the Y-m-d format

      if ($history->scheduleRelatingBackTo_Lesson) {
         $lesson = $history->scheduleRelatingBackTo_Lesson;
         $history->lesson_details = [
            'lesson_data' => $lesson->only(['id', 'title', 'schedule_day', 'start_time', 'end_time', 'status']),
            'teacher_details' => $lesson->lessonRelatingBackTo_Teacher,
            'student_details' => $lesson->lessonRelatingBackTo_Student,
            'instrument_details' => $lesson->lessonRelatingBackTo_Intrument,
         ];
      } else {
         $group = $history->scheduleRelatingBackTo_LessonGroup;
         $lessonDetails = [
            'group_lesson_data' => $group->only(['id', 'title', 'schedule_day', 'start_time', 'end_time', 'status']),
            'total_students' => $group->groupRelationWith_Students->count(),
            'teacher_details' => $group->groupRelatingBackTo_Teacher,
            'creator_details' => $group->groupRelatingBackTo_Creator,
            'instrument_details' => $group->groupRelatingBackTo_Intrument,
         ];

         if ($showStudents) {
            $lessonDetails['student_list'] = $group->groupRelationWith_Students->map(function ($student) {
               return ['membership_id' => $student->id, 'fees' => $student->fees, 'status' => $student->status, 'student_details' => $student->studentRelatingBackTo_User];
            })->values();
         }

         $history->lesson_details = $lessonDetails;
      }

      unset($history->scheduleRelatingBackTo_Lesson, $history->scheduleRelatingBackTo_LessonGroup, $history->scheduleRelatingBackTo_Creator, $history->scheduleRelatingBackTo_Modifier);
      return $history;
   }
}
