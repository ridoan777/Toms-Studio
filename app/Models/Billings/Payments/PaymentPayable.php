<?php

namespace App\Models\Billings\Payments;

use App\Models\Users\User;
use App\Models\Workflows\Lessons\{Lesson, LessonGroup, GroupStudent};
use Illuminate\Database\Eloquent\Attributes\{Table, Fillable};
use Illuminate\Database\Eloquent\Model;

#[Table('payment_payables')]
#[Fillable(['payable_uid', 'student_id', 'payable_type', 'payable_id', 'group_member_id', 'week_start', 'amount', 'status', 'paid_at', 'payment_id', 'created_by', 'is_cancelled', 'cancel_req_at', 'cancel_reason', 'cancel_resolve', 'cancelled_at'])]

class PaymentPayable extends Model
{
	protected function casts(): array
	{
		return [
			'student_id' => 'integer',
			'payable_id' => 'integer',
			'group_member_id' => 'integer',
			'week_start' => 'date',
			'amount' => 'integer',
			'status' => 'integer',
			'is_cancelled' => 'integer',
			'paid_at' => 'datetime',
			'cancel_req_at' => 'datetime',
			'cancelled_at' => 'datetime',
		];
	}

	// ----------------- RELATIONSHIPS -----------------

	public function payableRelatingBackTo_Student()
	{
		return $this->belongsTo(User::class, 'student_id');
	}

	public function payable()
	{
		return $this->morphTo();
	}

	public function payableRelatingBackTo_GroupMember()
	{
		return $this->belongsTo(GroupStudent::class, 'group_member_id');
	}
	// ----------------- RELATIONSHIPS -----------------

	// ----------------- MUTATOR + ACCESSOR -----------------
	public function getAmountAttribute($value)
	{
		return $value !== null ? $value / 100 : null;
	}

	public function setAmountAttribute($value)
	{
		$this->attributes['amount'] = $value !== null ? (int)round($value * 100) : null;
	}
	// ----------------- MUTATOR + ACCESSOR -----------------

	// ----------------- QUERY SCOPING -----------------
	public function scopePayableUid($query, string $payable_uid)
	{
		return $query->where('payable_uid', $payable_uid);
	}
	
	public function scopeStudentId($query, int $student_id)
	{
		return $query->where('student_id', $student_id);
	}

	public function scopeWeekStart($query, $week_start)
	{
		return $query->where('week_start', $week_start);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}

	public function scopeIsCancelled($query, $is_cancelled)
	{
		return $query->where('is_cancelled', $is_cancelled);
	}

	public function scopeUnpaid($query)
	{
		return $query->where('status', 0);
	}
	// ----------------- QUERY SCOPING -----------------
}