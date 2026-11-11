<?php

namespace App\Models\Billings\Subscriptions;

use App\Models\Workflows\Projects\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SubscriptionTier extends Model
{
	protected $table = 'subscription_tiers';

	protected $fillable = [
		'service_id',

		'platform',

		'name',
		'slogan',

		'starting_price',
		'final_price',

		'price_label',
		'duration',

		'benefits',
		'description',

		'rc_entitlement_id',
		'apple_product_id',
		'google_subscription_id',
		'google_base_plan_id',

		'stripe_product_id',
		'stripe_price_id',

		'image',
		'status',
	];

	protected $casts = [
		'starting_price' => 'float',
		'final_price' => 'float',
		'duration' => 'integer',
		'status' => 'integer',
	];

	protected $appends = ['image_url'];

	public function getImageUrlAttribute()
	{
		return $this->image ? Storage::url($this->image) : null;
	}

	public function subscriptionTiersRelationWith_course()
	{
		return $this->belongsTo(Project::class, 'service_id');
	}

	// ----------------- QUERY SCOPING -----------------
	public function scopePlatform($query, $platform)
	{
		return $query->where('platform', $platform);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
