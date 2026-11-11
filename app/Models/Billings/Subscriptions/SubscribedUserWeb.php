<?php

namespace App\Models\Billings\Subscriptions;

use App\Models\Users\User;
use App\Models\Workflows\Projects\Project;
use Illuminate\Database\Eloquent\Model;

class SubscribedUserWeb extends Model
{
	protected $table = "subscribed_user_webs";

	protected $fillable = [
		'user_id',
		'tier_id',
		'service_id',

		'payment_status',	// pending, paid
		'sub_status',	// requested, running, cancelled

		'amount',
		'duration',

		'note',
		'status',

		'invoice_id',
		'invoice_num',
		'next_renewal_at',
		'renewed_at',
		'cancelled_at',

		'payer',
		'last4',
		'brand',
		'expiry',

		'stripe_checkout_session_id',
		'stripe_subscription_id',
		'stripe_customer_id',

		'pdf_1st',
		'pdf_latest',
	];

	protected function casts(): array
	{
		return [
			'user_id' => 'integer',
			'tier_id' => 'integer',
			'service_id' => 'integer',
			'amount' => 'float',
			'status' => 'integer',
			'renewal_at' => 'datetime',
			'cancelled_at' => 'datetime',
		];
	}

	// ----------------- RELATIONSHIPS -----------------
	public function subscriberWebRelatingBackTo_User()	// they are subscriber
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function subscriberWebRelatingBackTo_Tier()	// tier = package
	{
		return $this->belongsTo(SubscriptionTier::class, 'tier_id');
	}

	public function subscriberWebRelationWith_Project()
	{
		return $this->belongsTo(Project::class, 'service_id');
	}
	// ----------------- RELATIONSHIPS -----------------


	// ----------------- QUERY SCOPING -----------------
	public function scopeActive($q)
	{
		return $q->where('status', 1)->whereNull('cancelled_at')
			->where(function ($q) {
				$q->whereNull('expiry')
					->orWhere('expiry', '>', now());
			});
	}

	public function scopeInactive($q)
	{
		return $q->where('status', 0)->orWhereNotNull('cancelled_at');
	}

	public function scopeUserId($query, $user_id)
	{
		return $query->where('user_id', $user_id);
	}

	public function scopeTierId($query, $tier_id)
	{
		return $query->where('tier_id', $tier_id);
	}

	public function scopeServiceId($query, $service_id)
	{
		return $query->where('service_id', $service_id);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
