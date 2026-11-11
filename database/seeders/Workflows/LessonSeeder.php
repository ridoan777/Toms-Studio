<?php

namespace Database\Seeders\Workflows;

use App\Helpers\UidGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Workflows\Lessons\Lesson;

class LessonSeeder extends Seeder
{
	public function run(): void
	{
		$phases = ['upcoming', 'in_progress', 'scheduled', 'completed', 'cancelled'];
		$difficulties = ['beginner', 'intermediate', 'advanced'];
		$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

		$titles = [
			'Piano Fundamentals',
			'Advanced Guitar Techniques',
			'Drum Groove Session',
			'Vocal Training Basics',
			'Stage Performance Practice',
			'Music Theory Workshop',
			'Jazz Improvisation',
			'Classical Violin Training',
			'Band Rehearsal Session',
			'Live Performance Prep',
			'Rhythm & Timing Mastery',
			'Beginner Keyboard Class',
			'Saxophone Expression Lab',
			'Creative Composition Session',
			'Studio Recording Practice'
		];

		for ($i = 0; $i < 50; $i++) {

			$teacherId = collect([5,6,7,8])->random();

			// more weight to 9,10
			$studentPool = array_merge([9,10,9,10,9,10], range(29,35));
			$studentId = collect($studentPool)->random();

			$startHour = rand(8, 20);
			$startMinute = rand(0, 59);

			$startTime = sprintf('%02d:%02d:00', $startHour, $startMinute);

			$endHour = min($startHour + rand(1, 3), 23);
			$endMinute = rand(0, 59);

			$endTime = sprintf('%02d:%02d:00', $endHour, $endMinute);

			$name =  $titles[array_rand($titles)];

			Lesson::create([
				'lesson_uid'   => UidGenerator::uniqueULID($instrument?->name ?? $name, 12, 18, 4),
				'teacher_id'   => $teacherId,
				'student_id'   => $studentId,
				'subject_id'   => rand(1, 15),
				'creator'      => collect([3,4])->random(),

				'title'        => $name,
				'phase'        => $phases[array_rand($phases)],
				'fees'         => rand(15, 100),
				'difficulty'   => $difficulties[array_rand($difficulties)],

				'schedule_day' => $days[array_rand($days)],
				'start_time'   => $startTime,
				'end_time'     => $endTime,

				'remarks'      => rand(0,1) ? 'Focus on technique and consistency.' : null,
				'last_class'   => rand(0,1) ? now()->subDays(rand(1,30)) : null,

				'status'       => 1,
				'created_at'   => now(),
				'updated_at'   => now(),
			]);
		}
	}
}