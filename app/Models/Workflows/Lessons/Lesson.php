<?php

namespace App\Models\Workflows\Lessons;

use App\Enums\LessonCategoryEnums;
use App\Models\Users\User;
use App\Models\Workflows\Instruments\Instrument;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\{Appends, Table, Fillable};
use Illuminate\Database\Eloquent\Model;

#[Table('lessons')]
#[Fillable(['lesson_uid', 'teacher_id', 'student_id', 'subject_id', 'creator', 'last_modifier', 'title', 'lesson_category', 'is_accepted', 'fees', 'difficulty', 'schedule_day', 'starting_date', 'start_time', 'end_time', 'remarks', 'status'])]
#[Appends(['lesson_duration_in_min'])]

class Lesson extends Model
{
	protected function casts(): array
	{
		return [
			'teacher_id' => 'integer',
			'student_id' => 'integer',
			'subject_id' => 'integer',
			'creator' => 'integer',
			'last_modifier' => 'integer',
			'lesson_category' => LessonCategoryEnums::class,
			'is_accepted' => 'integer',
			'schedule_day' => 'array',
			'starting_date' => 'date',
			'fees' => 'integer',
			'status' => 'integer',
		];
	}

	// ----------------- RELATIONSHIPS -----------------

	public function lessonRelatingBackTo_Teacher()
	{
		return $this->belongsTo(User::class, 'teacher_id');
	}

	public function lessonRelatingBackTo_Student()
	{
		return $this->belongsTo(User::class, 'student_id');
	}

	public function lessonRelatingBackTo_Creator()
	{
		return $this->belongsTo(User::class, 'creator');
	}

	public function lessonRelatingBackTo_Intrument()
	{
		return $this->belongsTo(Instrument::class, 'subject_id');
	}

	public function lessonRelatingBackTo_History()
	{
		return $this->hasMany(LessonHistory::class, 'lesson_id');
	}
	// ----------------- RELATIONSHIPS -----------------

	// ----------------- ACCESSORS -----------------
	public function getLessonDurationInMinAttribute()
	{
		if (!$this->start_time || !$this->end_time)
			return null;

		$start = Carbon::parse($this->start_time);
		$end = Carbon::parse($this->end_time);

		return $start->diffInMinutes($end);
	}
	// ----------------- ACCESSORS -----------------

	// ----------------- MUTATOR + ACCESSOR -----------------
	public function getFeesAttribute($value)
	{
		return $value !== null ? $value / 100 : null;
	}

	public function setFeesAttribute($value)
	{
		$this->attributes['fees'] = $value !== null ? (int)round($value * 100) : null;
	}
	// ----------------- MUTATOR + ACCESSOR -----------------

	// ----------------- QUERY SCOPING -----------------
	public function scopeTeacherId($query, int $teacher_id)
	{
		return $query->where('teacher_id', $teacher_id);
	}

	public function scopeStudentId($query, int $student_id)
	{
		return $query->where('student_id', $student_id);
	}

	public function scopeScheduleDay($query, $schedule_day)
	{
		return $query->whereJsonContains('schedule_day', $schedule_day);
	}

	public function isPendingMakeup(): bool
	{
		return $this->lesson_category === LessonCategoryEnums::MAKEUP
			&& (int)$this->is_accepted === 0;
	}

	public function scopeExceptMakeupPendings($query)
	{
		return $query->where(function ($q) {
			$q->where('lesson_category', '!=', LessonCategoryEnums::MAKEUP->value)
				->orWhere(function ($q2) {
					$q2->where('lesson_category', LessonCategoryEnums::MAKEUP->value)
						->where('is_accepted', 1);
				});
		});
	}

	public function scopeOnlyPendingMakeups($query)
	{
		return $query->where('lesson_category', LessonCategoryEnums::MAKEUP->value)
			->where('is_accepted', 0);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
