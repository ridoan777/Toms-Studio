<?php

namespace App\Models\Billings\Payments;

use App\Domains\Billings\Enums\TrxPhaseEnums;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\{Table, Fillable, Hidden};

#[Table('payment_transactions')]
#[Fillable(['user_id', 'trx_uid', 'ss_user_id', 'ss_user_email', 'payment_status', 'trx_phase', 'currency', 'paid_amount', 'stripe_fee', 'exchange_rate', 'takeaway_amount', 'card_details', 'customer_details', 'payment_intent', 'event_ids', 'stripe_checkout_session_id', 'note', 'status'])]

// #[Hidden(['payer', 'last4', 'brand', 'expiry', 'email', 'name', 'event_id', 'stripe_checkout_session_id', 'payment_intent'])]

class PaymentTrx extends Model
{
	protected function casts(): array
	{
		return [
			'user_id' => 'integer',
			'ss_user_id' => 'integer',
			'trx_phase' => TrxPhaseEnums::class,
			'paid_amount' => 'integer',
			'stripe_fee' => 'integer',
			'exchange_rate' => 'float',
			'takeaway_amount' => 'integer',
			'card_details' => 'array',
			'customer_details' => 'array',
			'event_ids' => 'array',
			'status' => 'integer',
		];
	}

	// ----------------- RELATIONSHIPS -----------------
	public function trxRelatingBackTo_User()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function trxRelatingBackTo_SnapshotUser()
	{
		return $this->belongsTo(User::class, 'ss_user_id');
	}

	public function trxRelationWith_Items()
	{
		return $this->hasMany(PaymentItem::class, 'payment_trx_id');
	}
	
	// ----------------- RELATIONSHIPS -----------------

	// ----------------- QUERY SCOPING -----------------

	public function scopeUserId($query, int $user_id)
	{
		return $query->where('user_id', $user_id);
	}

	public function scopeCheckoutSessionId($query, string $id)
	{
		return $query->where('stripe_checkout_session_id', $id);
	}

	public function scopePaymentIntent($query, string $intent)
	{
		return $query->where('payment_intent', $intent);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
