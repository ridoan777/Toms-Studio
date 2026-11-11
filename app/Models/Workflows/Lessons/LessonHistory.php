<?php

namespace App\Models\Workflows\Lessons;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Attributes\{Appends, Table, Fillable};
use Illuminate\Database\Eloquent\Model;

#[Table('lesson_histories')]
#[Fillable(['lesson_history_uid', 'lesson_id', 'group_id', 'phase', 'original_date', 'original_start_time', 'original_end_time', 'final_date', 'final_start_time', 'final_end_time', 'creator', 'last_modifier', 'status'])]

class LessonHistory extends Model
{
	protected function casts(): array
	{
		return [
			'lesson_id' => 'integer',
			'group_id' => 'integer',
			'original_date' => 'date',
			'original_start_time' => 'datetime',
			'original_end_time' => 'datetime',
			'final_date' => 'date',
			'final_start_time' => 'datetime',
			'final_end_time' => 'datetime',
			'creator' => 'integer',
			'last_modifier' => 'integer',
			'status' => 'integer',
		];
	}

	// ----------------- RELATIONSHIPS -----------------

	public function scheduleRelatingBackTo_Lesson()
	{
		return $this->belongsTo(Lesson::class, 'lesson_id');
	}

	public function scheduleRelatingBackTo_LessonGroup()
	{
		return $this->belongsTo(LessonGroup::class, 'group_id');
	}

	public function scheduleRelatingBackTo_Creator()
	{
		return $this->belongsTo(User::class, 'creator');
	}

	public function scheduleRelatingBackTo_Modifier()
	{
		return $this->belongsTo(User::class, 'last_modifier');
	}

	// ----------------- RELATIONSHIPS -----------------

	// ----------------- QUERY SCOPING -----------------
	public function scopeLessonId($query, int $lesson_id)
	{
		return $query->where('lesson_id', $lesson_id);
	}

	public function scopeGroupId($query, int $group_id)
	{
		return $query->where('group_id', $group_id);
	}

	public function scopePhase($query, string $phase)
	{
		return $query->where('phase', $phase);
	}

	public function scopeOriginalDate($query, $original_date)
	{
		return $query->where('original_date', $original_date);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
