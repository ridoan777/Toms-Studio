<?php

namespace App\Domains\Lessons\Services;

use App\Helpers\Errors\ExceptionHandling;
use App\Models\Workflows\Lessons\{GroupStudent, Lesson, LessonGroup, LessonNote};

class GetLessonNoteActorsService
{
   public function handle(?array $validated = null, ?LessonNote $note = null)
   {
      $lessonId = $validated['lesson_id'] ?? $note?->lesson_id;
      $groupId = $validated['group_id'] ?? $note?->group_id;
      $studentId = $validated['student_id'] ?? $note?->student_id;

      $targetLesson = !empty($lessonId) ? Lesson::findOrFail((int)$lessonId) : null;
      $targetGroupLesson = !empty($groupId) ? LessonGroup::findOrFail((int)$groupId) : null;

      $teacherId = $targetLesson
         ? (int)$targetLesson->teacher_id
         : ($targetGroupLesson ? (int)$targetGroupLesson->teacher_id : null);

      return [
         'target_lesson' => $targetLesson,
         'target_group' => $targetGroupLesson,
         'teacher_id' => $teacherId,
         // 'student_id' => array_map('intval', (array)$studentId),
         'student_id' => $studentId,
      ];
   }

   public function checkStudentEnrollment(?array $actorObject = [])
   {
      $targetLesson = $actorObject['target_lesson'] ?? null;
      $targetGroup = $actorObject['target_group'] ?? null;
      $studentIds = (array)($actorObject['student_id'] ?? []);

      if ($targetLesson) {
         if (count($studentIds) !== 1 || (int)$targetLesson->student_id !== (int)$studentIds[0]) {
            ExceptionHandling::bailout(403, "This student does not belong to this lesson!");
         }
      } elseif ($targetGroup) {
         $enrolledCount = GroupStudent::status(1)->where('group_id', $targetGroup->id)->whereHas('studentRelatingBackTo_User', fn($q) => $q->status(1)->whereIn('id', $studentIds))->count();

         if ($enrolledCount !== count($studentIds)) {
            ExceptionHandling::bailout(403, "One or more students do not belong to this group lesson!");
         }
      }

      return null;
   }
}