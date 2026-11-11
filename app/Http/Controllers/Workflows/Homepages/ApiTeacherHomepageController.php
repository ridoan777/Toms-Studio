<?php

namespace App\Http\Controllers\Workflows\Homepages;

use App\Domains\Lessons\Enums\LessonPhaseEnums;
use App\Helpers\ApiJsonReturnHelper;
use App\Http\Controllers\Controller;
use App\Helpers\Errors\ExceptionHandling;
use App\Models\Workflows\Lessons\{Lesson, LessonGroup, LessonNote, LessonHistory};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Throwable;


class ApiTeacherHomepageController extends Controller implements HasMiddleware
{
   use AuthorizesRequests;
   public static function middleware(): array
   {
      return [
         new Middleware('verify_role_key:admin,teacher', only: ['index']),
         // new Middleware(PermissionMiddleware::using('project_index'), only: ['index']),
      ];
   }
   // -----------------------------------------------

   public function index(Request $request)
   {
      try {
         $USER = $request->user();

         $todayDay = strtolower(Carbon::today()->format('l'));

         $lessons = Lesson::teacherId($USER->id)->status(1)->select(['id', 'lesson_uid', 'teacher_id', 'student_id', 'subject_id', 'title', 'lesson_category', 'difficulty', 'start_time', 'end_time', 'schedule_day'])->with('lessonRelatingBackTo_Student:id,name,avatar')->get();

         $groups = LessonGroup::teacherId($USER->id)->status(1)->select(['id', 'group_uid', 'teacher_id', 'subject_id', 'title', 'difficulty', 'start_time', 'end_time', 'schedule_day'])->with(['groupRelationWith_Students:id,member_uid,group_id,student_id', 'groupRelationWith_Students.studentRelatingBackTo_User:id,name,avatar'])->get();

         $widget = $this->teacherWidget($lessons, $groups, $todayDay);

         $lessonsToday = $lessons->filter(
            fn($lesson) =>
            in_array($todayDay, array_map('strtolower', $lesson->schedule_day ?? []))
         )->values();

         $groupsToday = $groups->filter(
            fn($group) =>
            in_array($todayDay, array_map('strtolower', $group->schedule_day ?? []))
         )->values();

         $lessons->transform(function ($item) {
            $item->student_details = $item->lessonRelatingBackTo_Student;
            $item->unsetRelation('lessonRelatingBackTo_Student');

            return $item;
         });

         $groups->transform(function ($item) {
            $item->membership_details = $item->groupRelationWith_Students->map(function ($member) {
               $member->student_details = $member->studentRelatingBackTo_User;
               unset($member->studentRelatingBackTo_User);

               return $member;
            });
            unset($item->groupRelationWith_Students);

            return $item;
         });

         $lessonIdsToday = $lessonsToday->pluck('id');
         $groupIdsToday = $groupsToday->pluck('id');

         $notesToday = LessonNote::status(1)
            ->where(function ($q) use ($lessonIdsToday, $groupIdsToday) {
               $q->whereIn('lesson_id', $lessonIdsToday)->orWhereIn('group_id', $groupIdsToday);
            })
            ->with([
               'noteRelatingBackTo_Lesson:id,lesson_uid,title,schedule_day',
               'noteRelatingBackTo_Group:id,schedule_day',
               'noteRelatingBackTo_Student:id,user_uid,username,name,user_role,avatar',
               'noteRelationWith_Gallery:id,note_id,filename,filepath'
            ])->latest()->limit(5)->get();

         $notesToday->transform(function ($note) {
            $note->lesson_details = $note->noteRelatingBackTo_Lesson;
            $note->group_details = $note->noteRelatingBackTo_Group;
            $note->student_details = $note->noteRelatingBackTo_Student;
            $note->galleries = $note->noteRelationWith_Gallery;

            unset($note->noteRelatingBackTo_Lesson);
            unset($note->noteRelatingBackTo_Group);
            unset($note->noteRelatingBackTo_Student);
            unset($note->noteRelationWith_Gallery);
            unset($note->created_at, $note->updated_at, $note->creator_id, $note->last_modifier_id);

            return $note;
         });

         return ApiJsonReturnHelper::handle(true, 200, "Teacher homepage data have been fetched successfully!", [
            'teacher_widget' => $widget,
            'schedules_today' => [
               'single_lessons' => $lessonsToday,
               'group_lessons' => $groupsToday,
            ],
            'quick_notes' => $notesToday
         ]);

      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching teacher homepage data', $e);
      }
   }
   // -----------------------------------------------

   private function teacherWidget($lessons, $groups, $todayDay): array
   {
      $todayLower = strtolower($todayDay);

      // --------------------- STUDENTS TODAY ---------------------
      $singleStudentIds = $lessons->filter(function ($lesson) use ($todayLower) {
         return in_array($todayLower, array_map('strtolower', $lesson->schedule_day ?? []));
      })->pluck('student_id');

      $groupStudentIds = $groups->filter(function ($group) use ($todayLower) {
         return in_array($todayLower, array_map('strtolower', $group->schedule_day ?? []));
      })->flatMap(function ($g) {
         return $g->groupRelationWith_Students->pluck('student_id');
      });

      $totalStudentsToday = $singleStudentIds->merge($groupStudentIds)->unique()->count();

      // --------------------- LESSONS COUNT ---------------------
      $totalLessons = $lessons->count() + $groups->count();

      $lessonIds = $lessons->pluck('id');
      $groupIds = $groups->pluck('id');

      $totalCompleted = LessonHistory::where('phase', LessonPhaseEnums::COMPLETED->value)
         ->where(function ($q) use ($lessonIds, $groupIds) {
            $q->whereIn('lesson_id', $lessonIds)->orWhereIn('group_id', $groupIds);
         })->count();

      $totalNoShow = LessonHistory::where('phase', LessonPhaseEnums::NO_SHOW->value)
         ->where(function ($q) use ($lessonIds, $groupIds) {
            $q->whereIn('lesson_id', $lessonIds)->orWhereIn('group_id', $groupIds);
         })->count();

      $totalSchedule = LessonHistory::where('phase', LessonPhaseEnums::SCHEDULED->value)
         ->where(function ($q) use ($lessonIds, $groupIds) {
            $q->whereIn('lesson_id', $lessonIds)->orWhereIn('group_id', $groupIds);
         })->count();

      return [
         'students_today' => $totalStudentsToday,
         'my_total_lessons' => $totalLessons,
         'my_total_completed_lessons' => $totalCompleted,
         'my_total_no_show_lessons' => $totalNoShow,
         'my_total_scheduled_lessons' => $totalSchedule,
      ];
   }
}