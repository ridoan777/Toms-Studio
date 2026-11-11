<?php

namespace App\Domains\Lessons\Services;

use App\Domains\Lessons\Enums\LessonPhaseEnums;
use App\Enums\LessonCategoryEnums;
use App\Helpers\UidGenerator;
use App\Models\Workflows\Lessons\{Lesson, LessonGroup, LessonHistory};
use Carbon\Carbon;

class HistoryGeneratorService
{
   public function generateForRange(Carbon $referenceDate, Carbon $rangeStart, Carbon $rangeEnd): array
   {
      $referenceDate = $referenceDate->copy()->startOfDay();
      $rangeStart = $rangeStart->copy()->startOfDay();
      $rangeEnd = $rangeEnd->copy()->endOfDay();

      $stats = [
         'lessons' => 0,
         'groups' => 0,
         'makeups' => 0,
         'today_progressed' => 0,
         'past_belated' => 0,
         'skipped' => 0,
      ];

      // ----- phase sync runs on every background execution -----
      $stats['today_progressed'] = LessonHistory::query()
         ->whereDate('final_date', $referenceDate->toDateString())
         ->where('phase', LessonPhaseEnums::UPCOMING->value)
         ->update(['phase' => LessonPhaseEnums::IN_PROGRESS->value]);

      $stats['past_belated'] = LessonHistory::query()
         ->whereDate('final_date', '<', $referenceDate->toDateString())
         ->where('phase', LessonPhaseEnums::UPCOMING->value)
         ->update(['phase' => LessonPhaseEnums::BELATED->value]);

      Lesson::query()
         ->status(1)
         // excluding lessons whose starting date is outside the range
         ->whereDate('starting_date', '<=', $rangeEnd->toDateString())
         ->get()
         ->each(function (Lesson $lesson) use ($referenceDate, $rangeStart, $rangeEnd, &$stats) {

            $lessonCategory = $lesson->lesson_category instanceof LessonCategoryEnums
               ? $lesson->lesson_category
               : LessonCategoryEnums::tryFrom((string)$lesson->lesson_category);

            // ----- accepted makeup lessons create exactly one future entry -----
            if ($lessonCategory === LessonCategoryEnums::MAKEUP) {
               if ((int)$lesson->is_accepted !== 1) {
                  $stats['skipped']++;
                  return;
               }

               $makeupDate = Carbon::parse($lesson->starting_date)->startOfDay();

               if ($makeupDate->lt($referenceDate) || $makeupDate->lt($rangeStart) || $makeupDate->gt($rangeEnd)) {
                  $stats['skipped']++;
                  return;
               }

               $history = $this->createHistoryIfMissing(
                  lessonId: $lesson->id,
                  groupId: null,
                  date: $makeupDate,
                  startTime: $this->normalizeTime($lesson->start_time),
                  endTime: $this->normalizeTime($lesson->end_time),
                  creatorId: (int)($lesson->creator ?? $lesson->teacher_id ?? 0),
                  lastModifierId: $lesson->last_modifier ? (int)$lesson->last_modifier : null,
               );

               if ($history) {
                  $stats['makeups']++;
               } else {
                  $stats['skipped']++;
               }

               return;
            }

            $scheduleDays = $this->normalizeScheduleDays($lesson->schedule_day ?? []);
            if (empty($scheduleDays)) {
               $stats['skipped']++;
               return;
            }

            foreach ($this->resolveScheduledDates($scheduleDays, $lesson->starting_date, $rangeStart, $rangeEnd) as $scheduledDate) {
               $history = $this->createHistoryIfMissing(
                  lessonId: $lesson->id,
                  groupId: null,
                  date: $scheduledDate,
                  startTime: $this->normalizeTime($lesson->start_time),
                  endTime: $this->normalizeTime($lesson->end_time),
                  creatorId: (int)($lesson->creator ?? $lesson->teacher_id ?? 0),
                  lastModifierId: $lesson->last_modifier ? (int)$lesson->last_modifier : null,
               );

               if ($history) {
                  $stats['lessons']++;
               } else {
                  $stats['skipped']++;
               }
            }
         });

      LessonGroup::query()
         ->status(1)
         ->whereDate('starting_date', '<=', $rangeEnd->toDateString())
         ->get()
         ->each(function (LessonGroup $group) use ($rangeStart, $rangeEnd, &$stats) {
            $scheduleDays = $this->normalizeScheduleDays($group->schedule_day ?? []);
            if (empty($scheduleDays)) {
               $stats['skipped']++;
               return;
            }

            foreach ($this->resolveScheduledDates($scheduleDays, $group->starting_date, $rangeStart, $rangeEnd) as $scheduledDate) {
               $history = $this->createHistoryIfMissing(
                  lessonId: null,
                  groupId: $group->id,
                  date: $scheduledDate,
                  startTime: $this->normalizeTime($group->start_time),
                  endTime: $this->normalizeTime($group->end_time),
                  creatorId: (int)($group->creator ?? $group->teacher_id ?? 0),
                  lastModifierId: $group->last_modifier ? (int)$group->last_modifier : null,
               );

               if ($history) {
                  $stats['groups']++;
               } else {
                  $stats['skipped']++;
               }
            }
         });

      return $stats;
   }

   private function createHistoryIfMissing(?int $lessonId, ?int $groupId, Carbon $date, ?string $startTime, ?string $endTime, int $creatorId, ?int $lastModifierId = null): ?LessonHistory
   {
      if (!$creatorId || !$startTime || !$endTime) {
         return null;
      }

      $history = LessonHistory::firstOrNew(
         [
            'lesson_id' => $lessonId,
            'group_id' => $groupId,
            'final_date' => $date->toDateString(),
         ],
         [
            'lesson_history_uid' => UidGenerator::uniqueULID($groupId ? 'HIST-GROUP' : 'HIST-LESSON', 12, 18, 4),
            'phase' => LessonPhaseEnums::UPCOMING->value,
            'original_date' => $date->toDateString(),
            'original_start_time' => $startTime,
            'original_end_time' => $endTime,
            'final_start_time' => $startTime,
            'final_end_time' => $endTime,
            'creator' => $creatorId,
            'last_modifier' => $lastModifierId,
            'status' => 1,
         ]
      );

      if (!$history->exists) {
         $history->save();
         return $history;
      }

      $shouldSyncTimes =
         $history->phase === LessonPhaseEnums::UPCOMING->value
         && (
            $history->original_start_time?->format('H:i:s') !== $startTime
            || $history->original_end_time?->format('H:i:s') !== $endTime
            || $history->final_start_time?->format('H:i:s') !== $startTime
            || $history->final_end_time?->format('H:i:s') !== $endTime
         );

      if ($shouldSyncTimes) {
         $history->original_start_time = $startTime;
         $history->original_end_time = $endTime;
         $history->final_start_time = $startTime;
         $history->final_end_time = $endTime;
         $history->last_modifier = $lastModifierId;
         $history->save();
      }

      return $history->wasRecentlyCreated ? $history : null;
   }

   private function resolveScheduledDates(array $scheduleDays, Carbon|string|null $startingDate, Carbon $rangeStart, Carbon $rangeEnd): array
   {
      $dates = [];
      $activeFrom = Carbon::parse($startingDate)->startOfDay();
      $leftBracket = $rangeStart->copy()->startOfDay();

      while ($leftBracket->lte($rangeEnd)) {
         if ($leftBracket->gte($activeFrom) && in_array(strtolower($leftBracket->format('l')), $scheduleDays, true)) {
            $dates[] = $leftBracket->copy();
         }

         $leftBracket->addDay();
      }
      return $dates;
   }

   private function normalizeScheduleDays(array|string|null $scheduleDays): array
   {
      $weekAliases = [
         'sun' => 'sunday',
         'sunday' => 'sunday',
         'mon' => 'monday',
         'monday' => 'monday',
         'tue' => 'tuesday',
         'tues' => 'tuesday',
         'tuesday' => 'tuesday',
         'wed' => 'wednesday',
         'wednesday' => 'wednesday',
         'thu' => 'thursday',
         'thurs' => 'thursday',
         'thursday' => 'thursday',
         'fri' => 'friday',
         'friday' => 'friday',
         'sat' => 'saturday',
         'saturday' => 'saturday',
      ];

      $scheduleDays = is_array($scheduleDays) ? $scheduleDays : [$scheduleDays];

      return collect($scheduleDays)->filter()
         ->map(fn($day) => strtolower(trim((string)$day)))
         ->map(fn($day) => $weekAliases[$day] ?? null)
         ->filter()->unique()->values()->all();
   }

   private function normalizeTime(Carbon|string|null $time): ?string
   {
      if (!$time) {
         return null;
      }

      return Carbon::parse($time)->format('H:i:s');
   }
}
