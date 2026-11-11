<?php

namespace App\Models\Billings\Subscriptions;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class SubscribedUserApp extends Model
{
	protected $table = 'subscribed_user_apps';

	protected $fillable = [
		'subscription_uid',
		'user_id',
		'tier_id',
		'entitlement_id',
		'provider',
		'store',

		'payment_status',	// pending, paid
		'sub_status',	// requested, running, cancelled, expired, changed

		'country',
		'currency',
		'amount',
		'duration',

		'note',
		'status',

		'rc_product_id',
		'rc_subscription_id',	// unique identifier
		'rc_purchase_token',	// unique identifier
		'rc_event_id',

		'purchased_at',
		'renewal_at',
		'cancelled_at',
	];

	protected function casts(): array
	{
		return [
			'user_id' => 'integer',
			'tier_id' => 'integer',
			'status' => 'integer',
			'purchased_at' => 'datetime',
			'renewal_at' => 'datetime',
			'cancelled_at' => 'datetime',
		];
	}

	// ----------------- RELATIONSHIPS -----------------
	public function subscribedAppUserRelatingBackTo_user()	// they are subscriber
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	
	public function subscribedAppUserRelatingBackTo_Tier()	// they are subscriber
	{
		return $this->belongsTo(SubscriptionTier::class, 'tier_id');
	}
	// ----------------- RELATIONSHIPS -----------------

	public function isActive(): bool
	{
		return ($this->status === 1) && (!$this->cancelled_at || ($this->sub_status !== 'cancelled'))
			&& (!$this->renewal_at || $this->renewal_at->isFuture());
	}


	// ----------------- QUERY SCOPING -----------------
	public function scopeActive($q)
	{
		return $q->where('status', 1)->whereNull('cancelled_at')
			->where(function ($q) {
				$q->whereNull('renewal_at')
					->orWhere('renewal_at', '>', now());
			});
	}

	public function scopeInactive($q)
	{
		return $q->where('status', 0)->orWhereNotNull('cancelled_at');
	}

	public function scopeUserId($query, int $user_id)
	{
		return $query->where('user_id', $user_id);
	}

	public function scopePayStatus($query, $payment_status)
	{
		return $query->where('payment_status', $payment_status);
	}

	public function scopeSubStatus($query, ...$sub_status)
	{
		return $query->whereIn('sub_status', $sub_status);
	}

	public function scopeSubscriptionId($query, $rc_subscription_id)
	{
		return $query->where('rc_subscription_id', $rc_subscription_id);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
/*
{
  "object": "subscription",
  "id": "sub1ab2c3d4e5",
  "customer_id": "19b8de26-77c1-49f1-aa18-019a391603e2",
  "original_customer_id": "19b8de26-77c1-49f1-aa18-019a391603e2",
  "product_id": "prod1a2b3c4d5e",
  "starts_at": 1658399423658,
  "current_period_starts_at": 1658399423658,
  "current_period_ends_at": 1658399423658,
  "ends_at": 1658399423658,
  "gives_access": true,
  "pending_payment": true,
  "auto_renewal_status": "will_renew",
  "status": "trialing",
  "total_revenue_in_usd": {
	 "currency": "USD",
	 "gross": 9.99,
	 "commission": 2.99,
	 "tax": 0.75,
	 "proceeds": 6.25
  },
  "presented_offering_id": "ofrnge1a2b3c4d5",
  "entitlements": {
	 "object": "list",
	 "items": [
		{
		  "state": "active",
		  "object": "entitlement",
		  "project_id": "proj1ab2c3d4",
		  "id": "entla1b2c3d4e5",
		  "lookup_key": "premium",
		  "display_name": "Premium",
		  "created_at": 1658399423658,
		  "products": {
			 "object": "list",
			 "items": [
				{
				  "state": "active",
				  "object": "product",
				  "id": "prod1a2b3c4d5e",
				  "store_identifier": "rc_1w_199",
				  "type": "subscription",
				  "subscription": {
					 "duration": "P1M",
					 "grace_period_duration": "P3D",
					 "trial_duration": "P1W"
				  },
				  "one_time": {
					 "is_consumable": true
				  },
				  "created_at": 1658399423658,
				  "app_id": "app1a2b3c4",
				  "app": {
					 "object": "app",
					 "id": "app1a2b3c4",
					 "name": "string",
					 "created_at": 1658399423658,
					 "type": "app_store",
					 "project_id": "proj1a2b3c4",
					 "amazon": {
						"package_name": "string"
					 },
					 "app_store": {
						"bundle_id": "string",
						"app_store_connect_api_key_configured": true,
						"subscription_key_configured": true
					 },
					 "mac_app_store": {
						"bundle_id": "string"
					 },
					 "play_store": {
						"package_name": "string"
					 },
					 "stripe": {
						"stripe_account_id": "string"
					 },
					 "rc_billing": {
						"stripe_account_id": "string",
						"seller_company_name": "string",
						"app_name": "string",
						"seller_company_support_email": "string",
						"support_email": "string",
						"default_currency": "USD"
					 },
					 "roku": {
						"roku_channel_id": "string",
						"roku_channel_name": "string"
					 },
					 "paddle": {
						"paddle_is_sandbox": true,
						"paddle_api_key": "stringstringstringstringstringstringstringstringst"
					 }
				  },
				  "display_name": "Premium Monthly 2023"
				}
			 ],
			 "next_page": "/v2/projects/proj1ab2c3d4/entitlements/entle1a2b3c4d5/products?starting_after=prodeab21dac",
			 "url": "/v2/projects/proj1ab2c3d4/entitlements/entle1a2b3c4d5/products"
		  }
		}
	 ],
	 "next_page": "/v2/projects/proj1ab2c3d4/subscriptions/sub1a2b3c4d5e/entitlements?status=active&starting_after=entlab21dac",
	 "url": "/v2/projects/proj1ab2c3d4/subscriptions/sub1a2b3c4d5e/entitlements"
  },
  "environment": "production",
  "store": "amazon",
  "store_subscription_identifier": 12345678,
  "ownership": "purchased",
  "pending_changes": {
	 "product": {
		"state": "active",
		"object": "product",
		"id": "prod1a2b3c4d5e",
		"store_identifier": "rc_1w_199",
		"type": "subscription",
		"subscription": {
		  "duration": "P1M",
		  "grace_period_duration": "P3D",
		  "trial_duration": "P1W"
		},
		"one_time": {
		  "is_consumable": true
		},
		"created_at": 1658399423658,
		"app_id": "app1a2b3c4",
		"app": {
		  "object": "app",
		  "id": "app1a2b3c4",
		  "name": "string",
		  "created_at": 1658399423658,
		  "type": "app_store",
		  "project_id": "proj1a2b3c4",
		  "amazon": {
			 "package_name": "string"
		  },
		  "app_store": {
			 "bundle_id": "string",
			 "app_store_connect_api_key_configured": true,
			 "subscription_key_configured": true
		  },
		  "mac_app_store": {
			 "bundle_id": "string"
		  },
		  "play_store": {
			 "package_name": "string"
		  },
		  "stripe": {
			 "stripe_account_id": "string"
		  },
		  "rc_billing": {
			 "stripe_account_id": "string",
			 "seller_company_name": "string",
			 "app_name": "string",
			 "seller_company_support_email": "string",
			 "support_email": "string",
			 "default_currency": "USD"
		  },
		  "roku": {
			 "roku_channel_id": "string",
			 "roku_channel_name": "string"
		  },
		  "paddle": {
			 "paddle_is_sandbox": true,
			 "paddle_api_key": "stringstringstringstringstringstringstringstringst"
		  }
		},
		"display_name": "Premium Monthly 2023"
	 }
  },
  "country": "US",
  "management_url": "https://apps.apple.com/account/subscriptions"
}

*/