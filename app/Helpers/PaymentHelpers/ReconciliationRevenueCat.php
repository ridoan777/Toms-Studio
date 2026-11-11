<?php

namespace App\Helpers\PaymentHelpers;

use App\Http\Controllers\Controller;
use App\Models\Billings\Subscriptions\SubscribedUserApp;
use GuzzleHttp\Client;

use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log};
use Throwable;

class ReconciliationRevenueCat
{

	public static function handle(?User $USER): bool
	{
		if (!$USER)
			return false;

		$getDbSubscription = SubscribedUserApp::userId($USER->id)->active()->latest()->first();

		if (!$getDbSubscription)
			return false;

		$client = new Client();
		$REVENUECAT_PROJECT_ID = config('services.revenuecat.project_id');
		// $REVENUECAT_PROJECT_ID = "proj1ab2c3d4";
		// $getDbSubscription['rc_subscription_id'] = "sub1a2b3c4d5e";

		if (!$REVENUECAT_PROJECT_ID)
			return false;

		try {
			$response = $client->get("https://api.revenuecat.com/v2/projects/" . $REVENUECAT_PROJECT_ID . "/subscriptions/{$getDbSubscription->rc_subscription_id}", [
				'headers' => [
					'Authorization' => 'Bearer ' . config('services.revenuecat.secret'),
					'Accept' => 'application/json',
				]
			]);

			return json_decode($response->getBody()->getContents(), true);

		} catch (\GuzzleHttp\Exception\ClientException $e) {

			$status = $e->getResponse()->getStatusCode();
			$body = json_decode($e->getResponse()->getBody()->getContents(), true);

			if ($status === 403 && isset($body['message']) && str_contains($body['message'], 'does not belong to the project')) {
				return false;
			}
			throw $e;
		}

		// $rvcSubscription = $response ? json_decode($response->getBody()->getContents(), true) : false;

		if (!$rvcSubscription)
			return false;

		self::reconcile($rvcSubscription, $getDbSubscription);
		return true;
	}
	// ----------------- RECONCILE SUBSCRIPTION -----------------

	public static function reconcile(array $rvcSubscription, $subscription): bool
	{
		return DB::transaction(function () use ($rvcSubscription, $subscription) {

			if ($rvcSubscription || !$subscription)
				return false;

			$status = $this->mapStatus($rvcSubscription);
			$paymentStatus = $rvcSubscription['pending_payment'] ? 'pending' : 'paid';

			$renewalAt = $rvcSubscription['current_period_ends_at'] ? now()->createFromTimestampMs($rvcSubscription['current_period_ends_at']) : null;

			$cancelledAt = $rvcSubscription['ends_at'] ? now()->createFromTimestampMs($rvcSubscription['ends_at'])
				: null;

			$amount = $rvcSubscription['total_revenue_in_usd']['gross'] ?? null;

			$entitlement = collect($rvcSubscription['entitlements']['items'] ?? [])->first();
			$entitlementKey = $entitlement['lookup_key'] ?? null;

			$subscription->update([
				'entitlement_id' => $entitlementKey,
				'store' => strtolower($rvcSubscription['store'] ?? $subscription->store),
				'country' => $rvcSubscription['country'] ?? $subscription->country,

				'payment_status' => $paymentStatus,
				'sub_status' => $status,
				'amount' => $amount,

				'note' => $subscription . ' ' . "(reconciled)",

				'renewal_at' => $renewalAt,
				'cancelled_at' => $cancelledAt,

				'status' => $rvcSubscription['gives_access'] ? 1 : 0,
			]);
			return true;
		});
	}
	// ----------------- RECONCILE SUBSCRIPTION -----------------


	// ----------------- STATUS MAPPING -----------------
	public static function mapStatus(array $rvcSubscription): string
	{
		return match ($rvcSubscription['status']) {
			'active', 'trialing' => 'running',
			'canceled' => 'cancelled',
			'expired' => 'expired',
			default => 'requested',
		};
	}
	// ----------------- STATUS MAPPING -----------------
}