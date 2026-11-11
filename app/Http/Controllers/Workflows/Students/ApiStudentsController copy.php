<?php

namespace App\Http\Controllers\Workflows\Students;

use App\Domains\Lessons\Enums\LessonPhaseEnums;
use App\Helpers\ApiJsonReturnHelper;
use App\Http\Controllers\Controller;
use App\Helpers\Errors\ExceptionHandling;
use App\Models\Users\User;
use App\Models\Workflows\Lessons\{Lesson, LessonGroup, LessonNote, LessonHistory};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Throwable;


class ApiStudentsControllerCopy extends Controller implements HasMiddleware
{
   use AuthorizesRequests;
   public static function middleware(): array
   {
      return [
         new Middleware('verify_role_key:admin,teacher', only: ['myStudents']),
         // new Middleware(PermissionMiddleware::using('project_index'), only: ['index']),
         // new Middleware(PermissionMiddleware::using('project_show'), only: ['show']),
      ];
   }
   // ------------------- FOR TEACHER ----------------------------

   public function myStudents(Request $request)
   {
      try {
         $USER = $request->user();

         $singleLessonStudents = Lesson::where('teacher_id', $USER->id)
            ->with(['lessonRelatingBackTo_Student:id,user_uid,username,name,user_role,avatar,status'])
            ->get()->pluck('lessonRelatingBackTo_Student');

         $groupLessons = LessonGroup::where('teacher_id', $USER->id)->with(['groupRelationWith_Students.studentRelatingBackTo_User:id,user_uid,username,name,user_role,avatar,status'])->get();

         $groupLessonStudents = collect();

         foreach ($groupLessons as $groupLesson) {
            foreach ($groupLesson->groupRelationWith_Students as $groupStudent) {
               if ($groupStudent->studentRelatingBackTo_User) {
                  $groupLessonStudents->push($groupStudent->studentRelatingBackTo_User);
               }
            }
         }

         /** @var $collection $allStudents */
         $allStudents = collect()->merge($singleLessonStudents)->merge($groupLessonStudents)->filter()->unique('id')->sortBy('id')->values();

         $lessonMap = Lesson::where('teacher_id', $USER->id)
            ->whereIn('student_id', $allStudents->pluck('id'))
            ->select(['id', 'lesson_uid', 'student_id', 'teacher_id', 'title'])
            ->get()
            ->groupBy('student_id');

         $groupMap = $groupLessons->flatMap(function ($groupLesson) {
            return $groupLesson->groupRelationWith_Students->map(function ($groupStudent) use ($groupLesson) {
               return [
                  'student_id' => $groupStudent->student_id,
                  'lesson' => [
                     'id' => $groupLesson->id,
                     'name' => $groupLesson->title,
                     // 'teacher' => $groupLesson?->groupRelatingBackTo_Teacher?->name,
                     'type' => 'group',
                     'group_lesson_uid' => $groupLesson->group_uid,
                  ],
               ];
            });
         })->groupBy('student_id');

         $allStudents = $allStudents->map(function ($student) use ($lessonMap, $groupMap) {
            $singleLessons = ($lessonMap->get($student->id) ?? collect())->map(function ($lesson) {
               return [
                  'id' => $lesson->id,
                  'name' => $lesson->title,
                  // 'teacher' => $lesson?->lessonRelatingBackTo_Teacher?->name,
                  'type' => 'lesson',
                  'lesson_uid' => $lesson->lesson_uid,
               ];
            });

            $groupLessons = ($groupMap->get($student->id) ?? collect())->pluck('lesson');

            $student->lessons = $singleLessons->merge($groupLessons)->values();

            return $student;
         });

         $page = (int)$request->input('page', 1);
         $perPage = 20;
         
         $paginatedStudents = new LengthAwarePaginator(
            $allStudents->forPage($page, $perPage)->values(),
            $allStudents->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
         );

         return ApiJsonReturnHelper::handle(true, 200, "My students are fetched successfully!", $allStudents);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching my students', $e);
      }
   }
   // -----------------------------------------------

   public function myStudentShow(Request $request, User $student)
   {
      try {
         $USER = $request->user();
         $studentId = $student->id;

         $studentLessonIds = Lesson::studentId($studentId)->pluck('id');
         $studentGroupIds = LessonGroup::whereHas('groupRelationWith_Students', function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
         })->pluck('id');
         $commonLessonIds = Lesson::teacherId($USER->id)->pluck('id');
         $commonGroupIds = LessonGroup::teacherId($USER->id)->pluck('id');

         // ------------------------ STUDENT WIDGET ------------------------

         $totalCommonLessons = $studentLessonIds->intersect($commonLessonIds)->count();
         $totalCommonGroups = $studentGroupIds->intersect($commonGroupIds)->count();

         $allHistories = LessonHistory::whereIn('phase', [
            LessonPhaseEnums::COMPLETED->value,
            LessonPhaseEnums::NO_SHOW->value
         ])
            ->where(function ($q) use ($studentLessonIds, $studentGroupIds) {
               $q->whereIn('lesson_id', $studentLessonIds)
                  ->orWhereIn('group_id', $studentGroupIds);
            })
            ->get(['id', 'phase']);

         $totalClasses = $allHistories->count();
         $totalCompletedOverall = $allHistories->where('phase', LessonPhaseEnums::COMPLETED->value)->count();
         $attendance = ($totalClasses && $totalCompletedOverall) ? (($totalCompletedOverall * 100) / $totalClasses) : null;

         $totalCompletedWithMe = LessonHistory::phase(LessonPhaseEnums::COMPLETED->value)
            ->where(function ($q) use ($studentLessonIds, $studentGroupIds, $commonLessonIds, $commonGroupIds) {
               $q->whereIn('lesson_id', $studentLessonIds->intersect($commonLessonIds))
                  ->orWhereIn('group_id', $studentGroupIds->intersect($commonGroupIds));
            })->count();

         $widget = [
            'total_lessons_overall' => $studentLessonIds->count() + $studentGroupIds->count(),
            'total_lessons_with_me' => $totalCommonLessons + $totalCommonGroups,
            // 'total_completed_classes_overall' => $totalCompletedOverall,
            // 'total_completed_classes_with_me' => $totalCompletedWithMe,
            // 'total_classes' => $totalClasses,
            'start_date' => $student?->created_at,
            'attendance_percentage_overall' => $attendance ? round($attendance, 2) : "N/A",
         ];
         // ------------------------ STUDENT WIDGET ------------------------

         // ------------------------ ALL NOTES WITH THIS TEACHER ------------------------
         $notes = LessonNote::status(1)->where('student_id', $studentId)
            ->where(function ($query) use ($commonLessonIds, $commonGroupIds) {
               $query->whereIn('lesson_id', $commonLessonIds)->orWhereIn('group_id', $commonGroupIds);
            })
            ->with([
               'noteRelatingBackTo_Lesson:id,lesson_uid,title,schedule_day',
               'noteRelatingBackTo_Group:id,group_uid,title,schedule_day',
               'noteRelationWith_Gallery:id,note_id,filename,filepath'
            ])->latest()->get();

         $notes->transform(function ($note) {
            $note->lesson_details = $note->noteRelatingBackTo_Lesson;
            $note->group_details = $note->noteRelatingBackTo_Group;
            $note->galleries = $note->noteRelationWith_Gallery;

            unset($note->noteRelatingBackTo_Lesson);
            unset($note->noteRelatingBackTo_Group);
            unset($note->noteRelationWith_Gallery);

            return $note;
         });
         // ------------------------ ALL NOTES WITH THIS TEACHER ------------------------

         // ------------------------ LESSONS & GROUPS ------------------------
         $lessons = Lesson::teacherId($USER->id)->studentId($studentId)->status(1)
            ->select(['id', 'lesson_uid', 'title', 'lesson_category', 'difficulty', 'start_time', 'end_time', 'schedule_day'])->get();

         $groups = LessonGroup::teacherId($USER->id)->status(1)
            ->whereHas('groupRelationWith_Students', function ($q) use ($studentId) {
               $q->where('student_id', $studentId);
            })
            ->select(['id', 'group_uid', 'title', 'difficulty', 'start_time', 'end_time', 'schedule_day'])
            ->get();
         // ------------------------ LESSONS & GROUPS ------------------------

         return ApiJsonReturnHelper::handle(true, 200, "Student notes fetched successfully!", [
            'student_details' => $student->only(['id', 'user_uid', 'username', 'name', 'avatar', 'avatar_url']),
            'student_widget' => $widget,
            'all_notes_with_me' => $notes,
            'all_lessons_with_me' => $lessons,
            'all_groups_with_me' => $groups,
         ]);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching student notes', $e);
      }
   }
}
