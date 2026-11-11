<?php

namespace App\Http\Controllers\Workflows\Lessons;

use App\Helpers\{UidGenerator, ApiJsonReturnHelper};
use App\Helpers\Auth\OwnershipAuthCheck;
use App\Http\Controllers\Controller;
use App\Helpers\Errors\ExceptionHandling;
use App\Models\Users\User;
use App\Models\Workflows\Lessons\{Lesson, LessonGroup, LessonHistory};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Illuminate\Validation\Rule;
use Throwable;

class ApiLessonController extends Controller implements HasMiddleware
{
   use AuthorizesRequests;
   public static function middleware(): array
   {
      return [
         new Middleware('verify_role_key:admin,student', only: ['statusMakeUp']),
         new Middleware(PermissionMiddleware::using('lesson_index'), only: ['index', 'myLessons']),
         new Middleware(PermissionMiddleware::using('lesson_show'), only: ['showSingle', 'showGroup']),
      ];
   }
   // -----------------------------------------------

   public function index(Request $request)
   {
      try {
         $USER = $request->user();

         $lessons = Lesson::with([
            'lessonRelatingBackTo_Teacher:id,user_uid,username,name,user_role,position,avatar,status',
            'lessonRelatingBackTo_Student:id,user_uid,username,name,user_role,guardian,avatar,status',
            'lessonRelatingBackTo_Intrument:id,name,status',
         ])->get();

         $lessonGroups = LessonGroup::with([
            'groupRelatingBackTo_Teacher:id,user_uid,username,name,user_role,position,avatar,status',
            'groupRelatingBackTo_Creator:id,user_uid,username,name,user_role,position,avatar,status',
            'groupRelatingBackTo_Intrument:id,name,status',
            'groupRelationWith_Students:id,member_uid,group_id,student_id,fees,status',
            'groupRelationWith_Students.studentRelatingBackTo_User:id,user_uid,username,name,user_role,position,avatar,status',
         ])->get();
         $lessons = $this->transformSingleCollection($lessons, $USER);
         $lessonGroups = $this->transformGroupCollection($lessonGroups);

         return ApiJsonReturnHelper::handle(true, 200, "All lessons have been fetched successfully!", [
            'single_lessons' => $lessons,
            'group_lessons' => $lessonGroups,
         ]);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching lessons', $e);
      }
   }
   // -----------------------------------------------

   public function myLessons(Request $request)
   {
      try {
         $USER = $request->user();

         $lessons = Lesson::with('lessonRelatingBackTo_Teacher:id,user_uid,username,name,user_role,position,avatar,timezone,status', 'lessonRelatingBackTo_Student:id,user_uid,username,name,user_role,guardian,avatar,timezone,status', 'lessonRelatingBackTo_Intrument')
            ->exceptMakeupPendings()
            ->where(function ($q1) use ($USER) {
               $q1->teacherId($USER->id)->orWhere(fn($q2) => $q2->studentId($USER->id));
            })->get();

         $lessonGroups = LessonGroup::with(
            'groupRelatingBackTo_Teacher:id,user_uid,username,name,user_role,position,avatar,timezone,status',
            'groupRelationWith_Students.studentRelatingBackTo_User:id,user_uid,username,name,user_role,avatar,status',
            'groupRelatingBackTo_Creator:id,user_uid,username,name,user_role,position,avatar,timezone,status',
            'groupRelatingBackTo_Intrument'
         )
            ->where(function ($q) use ($USER) {
               $q->where('teacher_id', $USER->id)
                  ->orWhereHas('groupRelationWith_Students', function ($q2) use ($USER) {
                     $q2->where('student_id', $USER->id);
                  });
            })
            ->get();

            $lessons = $this->transformSingleCollection($lessons, $USER);
         $lessonGroups = $this->transformGroupCollection($lessonGroups);

         return ApiJsonReturnHelper::handle(true, 200, "All my lessons fetched successfully!", [
            'single_lessons' => $lessons,
            'group_lessons' => $lessonGroups,
         ]);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching my lessons', $e);
      }
   }
   // -----------------------------------------------

   public function myMakeUps(Request $request)
   {
      try {
         $USER = $request->user();

         $lessons = Lesson::with('lessonRelatingBackTo_Teacher:id,user_uid,username,name,user_role,position,avatar,timezone,status', 'lessonRelatingBackTo_Student:id,user_uid,username,name,user_role,guardian,avatar,timezone,status', 'lessonRelatingBackTo_Intrument')
            ->onlyPendingMakeups()
            ->where(function ($q1) use ($USER) {
               $q1->teacherId($USER->id)->orWhere(fn($q2) => $q2->studentId($USER->id));
            })->get();

         $lessons = $this->transformSingleCollection($lessons, $USER);

         return ApiJsonReturnHelper::handle(true, 200, "All my lessons fetched successfully!", $lessons);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching my lessons', $e);
      }
   }
   // -----------------------------------------------

   public function statusMakeUp(Request $request, Lesson $makeUpLesson)
   {
      try {
         $USER = $request->user();
         OwnershipAuthCheck::ownerVsOwner($makeUpLesson?->student_id, $USER->id);

         if (!$makeUpLesson?->isPendingMakeup()) {
            ExceptionHandling::bailout(409, "This selected lesson is not a make-up lesson. Unable to perform this action!");
         }

         if ($makeUpLesson->is_accepted) {
            ExceptionHandling::bailout(409, "The make-up lesson was already been accepted. You cannot revert this!");
         }

         $makeUpLesson->update([
            'is_accepted' => 1,
            'status' => !$makeUpLesson->status,
         ]);

         return ApiJsonReturnHelper::handle(true, 200, "The  make-up lesson has been accepted successfully!", $makeUpLesson);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('accepting a make-up lesson', $e);
      }
   }
   // -----------------------------------------------

   public function showSingle(Request $request, Lesson $lesson)
   {
      try {
         $USER = $request->user();
         OwnershipAuthCheck::ownerInList([$lesson->teacher_id, $lesson->student_id], $USER->id, 'lesson');

         $lesson->load(
            'lessonRelatingBackTo_Teacher:id,user_uid,username,name,user_role,position,avatar,timezone,status',
            'lessonRelatingBackTo_Student:id,user_uid,username,name,user_role,guardian,avatar,timezone,status',
            'lessonRelatingBackTo_Intrument'
         );

         $lesson = $this->transformSingle($lesson, $USER);

         return ApiJsonReturnHelper::handle(true, 200, "The lesson has been fetched successfully!", $lesson);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching a lesson', $e);
      }
   }
   // -----------------------------------------------

   public function showGroup(Request $request, LessonGroup $lessonGroup)
   {
      try {
         $USER = $request->user();

         $isStudent = $lessonGroup->groupRelationWith_Students()->where('student_id', $USER->id)->exists();
         if ($USER->hasRoleKey('student') && !$isStudent) {
            ExceptionHandling::bailout(403, "Unauthorized! You are not allowed to access this lesson group");
         } else if ($USER->hasRoleKey('teacher')) {
            OwnershipAuthCheck::ownerInList([$lessonGroup->teacher_id], $USER->id, 'lesson group');
         }


         $lessonGroup->load(
            'groupRelatingBackTo_Teacher:id,user_uid,username,name,user_role,position,avatar,timezone,status',
            'groupRelationWith_Students.studentRelatingBackTo_User:id,user_uid,username,name,user_role,position,avatar,timezone,status',
            'groupRelatingBackTo_Creator:id,user_uid,username,name,user_role,position,avatar,timezone,status',
            'groupRelatingBackTo_Intrument'
         );

         // ---------- TRANSFORM ----------
         $lessonGroup->teacher_details = $lessonGroup->groupRelatingBackTo_Teacher;
         $lessonGroup->creator_details = $lessonGroup->groupRelatingBackTo_Creator;
         $lessonGroup->instrument_details = $lessonGroup->groupRelatingBackTo_Intrument;

         $lessonGroup->student_list = $lessonGroup->groupRelationWith_Students->map(function ($student) {
            return [
               'member_uid' => $student->member_uid,
               'fees' => $student->fees,
               'user' => $student->studentRelatingBackTo_User
            ];
         });

         unset($lessonGroup->groupRelatingBackTo_Teacher);
         unset($lessonGroup->groupRelatingBackTo_Creator);
         unset($lessonGroup->groupRelatingBackTo_Intrument);
         unset($lessonGroup->groupRelationWith_Students);

         return ApiJsonReturnHelper::handle(true, 200, "The lesson group has been fetched successfully!", $lessonGroup);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching a lesson group', $e);
      }
   }

   // ----------------------- HELPERS -----------------------

   private function transformSingle($lesson, User $USER)
   {
      $lesson->teacher_details = $lesson->lessonRelatingBackTo_Teacher;
      if (!$USER->hasRoleKey('student')) {
         $lesson->student_details = $lesson->lessonRelatingBackTo_Student;
      }
      $lesson->instrument_details = $lesson->lessonRelatingBackTo_Intrument;

      unset($lesson->lessonRelatingBackTo_Teacher);
      unset($lesson->lessonRelatingBackTo_Student);
      unset($lesson->lessonRelatingBackTo_Intrument);

      return $lesson;
   }
   // -----------------------------------------------

   private function transformGroup($lessonGroup)
   {
      $lessonGroup->teacher_details = $lessonGroup->groupRelatingBackTo_Teacher;
      $lessonGroup->creator_details = $lessonGroup->groupRelatingBackTo_Creator;
      $lessonGroup->instrument_details = $lessonGroup->groupRelatingBackTo_Intrument;
      $lessonGroup->student_list = $lessonGroup->groupRelationWith_Students->map(function ($student) {
         return [
            'member_uid' => $student->member_uid,
            'fees' => $student->fees,
            'status' => $student->status,
            'user' => $student->studentRelatingBackTo_User,
         ];
      });
      unset($lessonGroup->groupRelatingBackTo_Teacher);
      unset($lessonGroup->groupRelatingBackTo_Creator);
      unset($lessonGroup->groupRelatingBackTo_Intrument);
      unset($lessonGroup->groupRelationWith_Students);
      return $lessonGroup;
   }
   // -----------------------------------------------

   private function transformSingleCollection($collection, ?User $USER = null)
   {
      return $collection->transform(fn($item) => $this->transformSingle($item, $USER));
   }
   // -----------------------------------------------

   private function transformGroupCollection($collection)
   {
      return $collection->transform(fn($item) => $this->transformGroup($item));
   }
   // -----------------------------------------------
}