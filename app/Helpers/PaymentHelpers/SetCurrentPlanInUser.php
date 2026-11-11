<?php
namespace App\Helpers\PaymentHelpers;
use App\Models\Users\User;

class SetCurrentPlanInUser
{
	public static function planFromProductId(?string $productId): string
	{
		return match ($productId) {
			'bf_premium_month:revenue2026' => 'monthly',
			'bf_premium_year:year' => 'yearly',
			'rc_2026_1m' => 'monthly',
			'rc_2026_1y' => 'yearly',
			default => 'free',
		};
	}
	// --------------------------------------

	public static function setUserPlanPaid(?User $user = null, ?array $event = null): void
	{
		if($user && $event){
			$plan = self::planFromProductId($event['product_id'] ?? null);
	
			$user->current_plan = $plan;
			$user->save();
		}
	}
	// --------------------------------------

	public static function setUserPlanFree(?User $user = null): void
	{
		if($user){
			$user->current_plan = 'free';
			$user->save();
		}
	}
}