<?php

namespace Database\Seeders\Workflows;

use App\Helpers\UidGenerator;
use Illuminate\Database\Seeder;
use App\Models\Workflows\Lessons\{LessonGroup, GroupStudent};

class LessonGroupSeeder extends Seeder
{
	public function run(): void
	{
		$groupCount = 2;
		$minStudentsPerGroup = 30;
		$maxStudentsPerGroup = 60;

		$difficulties = ['beginner', 'intermediate', 'advanced'];
		$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

		$titles = [
			// 'Piano Fundamentals',
			// 'Advanced Guitar Techniques',
			// 'Drum Groove Session',
			// 'Vocal Training Basics',
			// 'Stage Performance Practice',
			// 'Music Theory Workshop',
			// 'Jazz Improvisation',
			'Classical Violin Training',
			// 'Band Rehearsal Session',
			// 'Live Performance Prep',
			// 'Rhythm & Timing Mastery',
			'Beginner Keyboard Class',
			// 'Saxophone Expression Lab',
			// 'Creative Composition Session',
			// 'Studio Recording Practice',
			// 'Flute Theory Workshop',
			// 'Guitar Maestro',
			// 'Flute Expression Lab',
		];

		$teacherIds = range(5, 22);
		$studentPool = array_merge([8, 9, 10], range(30, 80));
		$creatorIds = [3, 4];

		for ($i = 0; $i < $groupCount; $i++) {
			$title = $titles[array_rand($titles)];
			$creatorId = $creatorIds[array_rand($creatorIds)];
			$scheduleDays = collect($days)->shuffle()->take(rand(1, 3))->values()->toArray();

			$startHour = rand(8, 20);
			$startMinute = collect([0, 10, 15, 20, 25, 30, 35, 40, 45, 50])->random();
			$durationMinutes = collect([45, 50, 55, 60, 65, 70])->random();

			$startTimestamp = strtotime(sprintf('%02d:%02d:00', $startHour, $startMinute));
			$endTimestamp = $startTimestamp + ($durationMinutes * 60);

			$group = LessonGroup::create([
				'group_uid' => UidGenerator::uniqueULID($title, 12, 18, 4),
				'teacher_id' => $teacherIds[array_rand($teacherIds)],
				'subject_id' => rand(1, 15),
				'title' => $title,
				'difficulty' => $difficulties[array_rand($difficulties)],
				'schedule_day' => $scheduleDays,
				'starting_date' => now()->subDays(rand(0, 70))->toDateString(),
				'start_time' => date('H:i:s', $startTimestamp),
				'end_time' => date('H:i:s', $endTimestamp),
				'remarks' => rand(0, 1) ? '<p>Focus on technique and consistency.</p>' : null,
				'creator' => $creatorId,
				'last_modifier' => rand(0, 1) ? $creatorId : null,
				'status' => rand(1, 10) === 1 ? 0 : 1,
				'created_at' => now()->subDays(rand(1, 80)),
				'updated_at' => now()->subDays(rand(0, 30)),
			]);

			$uniqueStudentPool = collect($studentPool)->unique()->values();
			$studentCount = min(rand($minStudentsPerGroup, $maxStudentsPerGroup), $uniqueStudentPool->count());
			$selectedStudents = $uniqueStudentPool->shuffle()->take($studentCount);

			foreach ($selectedStudents as $studentId) {
				GroupStudent::create([
					'member_uid' => UidGenerator::uniqueULID("MEMBER-" . $group->title, 12, 18, 4),
					'group_id' => $group->id,
					'student_id' => $studentId,
					'fees' => rand(0, 37),
					'adder' => $group->creator,
					'last_modifier' => rand(0, 1) ? $group->creator : null,
					'status' => 1,
					'created_at' => $group->created_at,
					'updated_at' => $group->updated_at,
				]);
			}
		}
	}
}
