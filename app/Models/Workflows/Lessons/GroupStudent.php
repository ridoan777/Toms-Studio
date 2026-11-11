<?php

namespace App\Models\Workflows\Lessons;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Attributes\{Appends, Table, Fillable};
use Illuminate\Database\Eloquent\Model;

#[Table('lesson_group_students')]
#[Fillable(['member_uid', 'group_id', 'student_id', 'fees', 'adder', 'last_modifier', 'status'])]

class GroupStudent extends Model
{
	protected function casts(): array
	{
		return [
			'group_id' => 'integer',
			'student_id' => 'integer',
			'adder' => 'integer',
			'last_modifier' => 'integer',
			'fees' => 'integer',
			'status' => 'boolean',
		];
	}

	// ----------------- RELATIONSHIPS -----------------

	public function studentRelatingBackTo_Group()
	{
		return $this->belongsTo(LessonGroup::class, 'group_id');
	}

	public function studentRelatingBackTo_User()
	{
		return $this->belongsTo(User::class, 'student_id');
	}

	public function studentRelatingBackTo_Adder()
	{
		return $this->belongsTo(User::class, 'adder');
	}
	// ----------------- RELATIONSHIPS -----------------

	// ----------------- MUTATOR + ACCESSOR -----------------
	public function getFeesAttribute($value)
	{
		return $value !== null ? $value / 100 : null;
	}

	public function setFeesAttribute($value)
	{
		$this->attributes['fees'] = $value !== null
			? (int)round($value * 100)
			: null;
	}
	// ----------------- MUTATOR + ACCESSOR -----------------


	// ----------------- QUERY SCOPING -----------------
	public function scopeGroupId($query, int $group_id)
	{
		return $query->where('group_id', $group_id);
	}

	public function scopeStudentId($query, int $student_id)
	{
		return $query->where('student_id', $student_id);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
