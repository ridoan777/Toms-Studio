<?php

namespace App\Models\Workflows\Lessons;

use App\Models\Users\User;
use App\Models\Workflows\Instruments\Instrument;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\{Appends, Table, Fillable};
use Illuminate\Database\Eloquent\Model;

#[Table('lesson_groups')]
#[Fillable(['group_uid', 'teacher_id', 'subject_id', 'title', 'difficulty', 'schedule_day', 'starting_date', 'start_time', 'end_time', 'remarks', 'creator', 'last_modifier', 'status'])]
#[Appends(['lesson_duration_in_min'])]

class LessonGroup extends Model
{
	protected function casts(): array
	{
		return [
			'teacher_id' => 'integer',
			'subject_id' => 'integer',
			'schedule_day' => 'array',
			'starting_date' => 'date',
			'creator' => 'integer',
			'last_modifier' => 'integer',
			'status' => 'integer',
		];
	}

	// ----------------- RELATIONSHIPS -----------------

	public function groupRelatingBackTo_Teacher()
	{
		return $this->belongsTo(User::class, 'teacher_id');
	}

	public function groupRelationWith_Students()
	{
		return $this->hasMany(GroupStudent::class, 'group_id');
	}

	public function groupRelatingBackTo_Creator()
	{
		return $this->belongsTo(User::class, 'creator');
	}

	public function groupRelatingBackTo_Intrument()
	{
		return $this->belongsTo(Instrument::class, 'subject_id');
	}
	
	public function groupRelatingBackTo_History()
	{
		return $this->hasMany(LessonHistory::class, 'group_id');
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

	// ----------------- QUERY SCOPING -----------------
	public function scopeTeacherId($query, int $teacher_id)
	{
		return $query->where('teacher_id', $teacher_id);
	}

	public function scopeScheduleDay($query, $schedule_day)
	{
		return $query->where('schedule_day', $schedule_day);
	}

	public function scopePhase($query, $phase)
	{
		return $query->where('phase', $phase);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
