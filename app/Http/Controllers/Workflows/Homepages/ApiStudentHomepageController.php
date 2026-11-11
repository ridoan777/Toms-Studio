<?php

namespace App\Http\Controllers\Workflows\Homepages;

use App\Helpers\ApiJsonReturnHelper;
use App\Http\Controllers\Controller;
use App\Helpers\Errors\ExceptionHandling;
use App\Models\Workflows\Lessons\{Lesson, LessonGroup};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Throwable;


class ApiStudentHomepageController extends Controller implements HasMiddleware
{
   use AuthorizesRequests;
   public static function middleware(): array
   {
      return [
         new Middleware('verify_role_key:admin,student', only: ['index']),
      ];
   }
   // -----------------------------------------------

   public function index(Request $request)
   {
      try {
         $USER = $request->user();
         $timezone = $USER?->timezone ?? config('app.timezone');

         $lessons = Lesson::studentId($USER->id)
            ->status(1)
            ->select(['id', 'lesson_uid', 'teacher_id', 'student_id', 'subject_id', 'title', 'lesson_category', 'difficulty', 'start_time', 'end_time', 'schedule_day'])
            ->with('lessonRelatingBackTo_Teacher:id,name,avatar')
            ->get();

         $groups = LessonGroup::whereHas('groupRelationWith_Students', function ($q) use ($USER) {
            $q->where('student_id', $USER->id);
         })
            ->status(1)
            ->select(['id', 'group_uid', 'teacher_id', 'subject_id', 'title', 'difficulty', 'start_time', 'end_time', 'schedule_day'])
            ->with([
               'groupRelatingBackTo_Teacher:id,name,avatar',
               'groupRelationWith_Students' => function ($q) use ($USER) {
                  $q->select(['id', 'member_uid', 'group_id', 'student_id'])
                     ->where('student_id', $USER->id)
                     ->with('studentRelatingBackTo_User:id,name,avatar');
               }
            ])->get();

         $targetDay = null;
         $targetDate = null;

         for ($i = 0; $i < 7; $i++) {  // must start with $i=0

            $checkingDate = Carbon::today()->addDays($i);
            $checkingDay = strtolower(Carbon::today()->addDays($i)->format('l'));

            $lessonsToday = $lessons->filter(
               fn($lesson) => in_array($checkingDay, array_map('strtolower', $lesson->schedule_day ?? []))
            )->values();

            $groupsToday = $groups->filter(
               fn($group) => in_array($checkingDay, array_map('strtolower', $group->schedule_day ?? []))
            )->values();

            if ($lessonsToday->count() > 0 || $groupsToday->count() > 0) {
               $targetDay = $checkingDay;
               $targetDate = $checkingDate->copy()->startOfDay()->utc()->toIso8601String();
               break;
            }
         }

         $lessons->transform(function ($item) {
            $item->teacher_details = $item->lessonRelatingBackTo_Teacher;
            $item->unsetRelation('lessonRelatingBackTo_Teacher');

            return $item;
         });

         $groups->transform(function ($item) {
            $item->teacher_details = $item->groupRelatingBackTo_Teacher;
            $item->membership_details = $item->groupRelationWith_Students->first();

            unset($item->groupRelatingBackTo_Teacher);
            unset($item->groupRelationWith_Students);
            unset($item->membership_details->studentRelatingBackTo_User);

            return $item;
         });

         return ApiJsonReturnHelper::handle(true, 200, "Student homepage data have been fetched successfully!", [
            'schedules_today' => [
               'single_lessons' => $lessonsToday,
               'group_lessons' => $groupsToday,
            ],
            'schedule_info' => [
               'day' => $targetDay,
               'date' => $targetDate,
            ],
         ]);

      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching student homepage data', $e);
      }
   }
   // -----------------------------------------------
}