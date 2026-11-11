<?php

namespace Database\Seeders\Workflows;

use App\Helpers\UidGenerator;
use App\Models\Workflows\Lessons\LessonHistory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
/*
	valid id: 9 - 34
*/
class LessonHistorySeeder extends Seeder
{
	public function run(): void
	{
		$phases = ['upcoming', 'in_progress', 'scheduled', 'completed', 'cancelled'];
		$lessonIds = [1, 12, 13, 14];
		$groupIds = [1, 2, 3, 4];
		$userIds = [3, 5, 6, 7];
		$startDate = Carbon::create(2026, 6, 1);
		$endDate = Carbon::create(2026, 8, 15);
		$totalRecords = 50;
		$totalDays = $startDate->diffInDays($endDate);
		$timeSlots = [
			['10:15:00', '11:15:00'],
			['10:25:00', '11:30:00'],
			['10:30:00', '11:30:00'],
			['10:35:00', '11:30:00'],
			['11:25:00', '12:35:00'],
		];
		$dateRecordGroups = array_merge(
			array_fill(0, 13, 1),
			array_fill(0, 6, 2),
			array_fill(0, 5, 3),
			[5, 5]
		);
		$totalDateGroups = count($dateRecordGroups);
		$recordIndex = 0;

		foreach ($dateRecordGroups as $dateGroupIndex => $recordsPerDate) {
			$dayOffset = $totalDateGroups > 1
				? (int) round(($dateGroupIndex * $totalDays) / ($totalDateGroups - 1))
				: 0;
			$finalDate = $startDate->copy()->addDays($dayOffset);

			for ($slotIndex = 0; $slotIndex < $recordsPerDate; $slotIndex++, $recordIndex++) {
				$phase = $phases[$recordIndex % count($phases)];
				$lessonId = $lessonIds[$recordIndex % count($lessonIds)];
				$groupId = $groupIds[$recordIndex % count($groupIds)];
				$creator = $userIds[$recordIndex % count($userIds)];
				$lastModifier = $userIds[($recordIndex + 1) % count($userIds)];
				[$finalStartTime, $finalEndTime] = $timeSlots[$recordIndex % count($timeSlots)];
				$hourShift = $slotIndex * 10;
				$finalStartTime = Carbon::parse($finalStartTime)->addMinutes($hourShift)->format('H:i:s');
				$finalEndTime = Carbon::parse($finalEndTime)->addMinutes($hourShift)->format('H:i:s');

				$originalDate = $phase === 'scheduled'
					? $finalDate->copy()->subDays(1)
					: ($recordIndex % 3 === 0 ? $finalDate->copy()->subDays(2) : null);

				LessonHistory::create([
					'lesson_history_uid' => UidGenerator::uniqueULID('HIST-LESSON-GROUP', 12, 18, 4),
					'lesson_id' => $lessonId,
					'group_id' => $groupId,
					'phase' => $phase,
					'original_date' => $originalDate?->toDateString(),
					'original_start_time' => $originalDate ? $finalStartTime : null,
					'original_end_time' => $originalDate ? $finalEndTime : null,
					'final_date' => $finalDate->toDateString(),
					'final_start_time' => $finalStartTime,
					'final_end_time' => $finalEndTime,
					'creator' => $creator,
					'last_modifier' => $recordIndex % 2 === 0 ? $lastModifier : null,
					'status' => 1,
					'created_at' => $finalDate->copy()->subDays(5)->setTime(8, 30),
					'updated_at' => $finalDate->copy()->subDays(1)->setTime(9, 15),
				]);
			}
		}
	}
}
