<?php

namespace App\Actions\Billings;

use App\Models\Billings\Subscriptions\SubscriptionTier;

class SubscriptionTierSaveAction
{
	public function execute(array $validated): SubscriptionTier
	{
		return SubscriptionTier::updateOrCreate(
			['id' => $validated['id'] ?? null],
			[
				'service_id' => $validated['service_id'] ?? null,
				'platform' => $validated['platform'],

				'name' => $validated['name'],
				'slogan' => $validated['slogan'] ?? null,

				'starting_price' => $validated['starting_price'],
				'final_price' => $validated['final_price'],

				'price_label' => $validated['price_label'] ?? null,
				'duration' => $validated['duration'] ?? null,
				'benefits' => $validated['benefits'] ?? null,
				'description' => $validated['description'] ?? null,

				'rc_entitlement_id' => $validated['rc_entitlement_id'] ?? null,
				'apple_product_id' => $validated['apple_product_id'] ?? null,
				'google_subscription_id' => $validated['google_subscription_id'] ?? null,
				'google_base_plan_id' => $validated['google_base_plan_id'] ?? null,

				'stripe_product_id' => $validated['stripe_product_id'] ?? null,
				'stripe_price_id' => $validated['stripe_price_id'] ?? null,

				'image' => $validated['image']['path'] ?? null,

				'status' => $validated['status'] ?? 1,
			]
		);
	}
}
