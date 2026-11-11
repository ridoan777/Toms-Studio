<?php

namespace App\Models\Billings\Payments;

use App\Models\Users\User;
use App\Domains\Billings\Enums\ItemPhaseEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\{Table, Fillable, Hidden};

#[Table('payment_items')]
#[Fillable(['payment_trx_id', 'payment_item_uid', 'lesson_id', 'group_id', 'payer_id', 'student_id', 'phase', 'ss_payer_name', 'ss_payer_email', 'ss_student_name', 'ss_student_email', 'ss_currency', 'ss_amount', 'stripe_fee_share', 'note', 'status'])]

class PaymentItem extends Model
{
	protected function casts(): array
	{
		return [
			'payment_trx_id' => 'integer',
			'lesson_id' => 'integer',
			'group_id' => 'integer',
			'payer_id' => 'integer',
			'student_id' => 'integer',
			'phase' => ItemPhaseEnums::class,
			'ss_amount' => 'integer',
			'stripe_fee_share' => 'integer',
			'status' => 'integer',
		];
	}

	// ----------------- RELATIONSHIPS -----------------
	public function trxRelatingBackTo_Payer()
	{
		return $this->belongsTo(User::class, 'payer_id');
	}
	public function trxRelatingBackTo_Student()
	{
		return $this->belongsTo(User::class, 'student_id');
	}

	public function itemRelatingBackTo_Trx()
	{
		return $this->belongsTo(PaymentTrx::class, 'payment_trx_id');
	}
	// ----------------- RELATIONSHIPS -----------------

	// ----------------- QUERY SCOPING -----------------

	public function scopePaymentTrxId($query, int $payment_trx_id)
	{
		return $query->where('payment_trx_id', $payment_trx_id);
	}

	public function scopePayableUid($query, string $payment_item_uid)
	{
		return $query->where('payment_item_uid', $payment_item_uid);
	}

	public function scopePhase($query, int $payer_id)
	{
		return $query->where('payer_id', $payer_id);
	}

	public function scopePayerId($query, int $payer_id)
	{
		return $query->where('payer_id', $payer_id);
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
/*
$300
$9
item-1 = $100
(fee * part)/$total


======
item-2 = $200


*/