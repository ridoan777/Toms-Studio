<?php

namespace App\Helpers\PaymentHelpers;
use App\Helpers\ApiJsonReturnHelper;
use App\Models\Billings\Subscriptions\SubscribedUserApp;
use Carbon\Carbon;

use function Symfony\Component\Clock\now;

class SubscriptionCheckerApp
{
	// --------------------------------------
	public static function validityGate($USER)
	{
		// $subscriber = SubscribedUserApp::where('user_id', $USER->id)->latest()->first();

		$subscriber = SubscribedUserApp::where('user_id', $USER->id)
			->where('processing_status', 'applied')->where('payment_status', 'paid')
			// ->whereIn('sub_status', ['running', 'changed', 'cancelled', 'billing_issue', 'refunded'])
			->whereNotNull('renewal_at')
			// ->where('renewal_at', '>', now())
			->orderByDesc('purchased_at')->first();

		if (!$subscriber) {
			return self::userNotSubscriber();
		}

		if (strtolower($subscriber->payment_status) !== 'paid') {
			return self::notValidSubscription($subscriber, 'unpaid');
		}

		if (strtolower($subscriber->sub_status) === 'expired') {
			return self::notValidSubscription($subscriber, 'expired');
		}

		// ---------------------- TIME CHECKING ----------------------
		$nowEpoch = now()->getTimestamp();
		$renewalEpoch = $subscriber->renewal_at ? $subscriber->renewal_at->getTimestamp() : null;

		if (!$renewalEpoch || $renewalEpoch <= $nowEpoch) {
			return self::notValidSubscription($subscriber, 'expired');
		}
		// ---------------------- TIME CHECKING ----------------------

		$allowedStates = ['running', 'changed', 'cancelled', 'billing_issue', 'refunded'];

		if (!in_array(strtolower($subscriber->sub_status), $allowedStates, true)) {
			return self::notValidSubscription($subscriber, 'stopped');
		}

		$secureData = null;
		if (app()->environment('local')) {
			$secureData = $subscriber;
		} else {
			$secureData = $subscriber->makeHidden(['rc_product_id', 'rc_subscription_id', 'rc_purchase_token', 'rc_event_id', 'rc_event_id', 'processing_status']);
		}

		return [
			'status' => true,
			'data' => $secureData,
			'code' => 200
		];
	}
	// --------------------------------------

	public static function userNotSubscriber()
	{
		return [
			'status' => false,
			'message' => "Unauthorized! The current user is not a subscriber!",
			'code' => 403
		];
	}
	// --------------------------------------

	public static function notValidSubscription($subscriber, $FLAG)
	{
		$MESSAGE = null;
		switch ($FLAG) {
			case 'unpaid':
				$MESSAGE = "Aborting! You haven't paid for this subscription!";
				break;

			case 'expired':
				$MESSAGE = "Aborting! Your subscription is expired! Renew to access!";
				break;

			case 'stopped':
				$MESSAGE = "Aborting! Subscription is not running yet!";
				break;

			default:
				$MESSAGE = "Aborting! Failed to check subscription!";
		}

		return [
			'status' => false,
			'message' => $MESSAGE,
			'rvc_data' => [
				'entitlement_id' => $subscriber->entitlement_id ?? 'No ENTITLEMENT_ID found',
				'provider' => $subscriber->provider ?? 'No PROVIDER found',
				'store' => $subscriber->store ?? 'No STORE found',

				'payment_status' => $subscriber->payment_status ?? 'No PAYMENT_STATUS found',
				'sub_status' => $subscriber->sub_status ?? 'No SUBSCRIPTION_STATUS found',

				'country_code' => $subscriber->country ?? 'No COUNTRY_CODE found',
				'currency' => $subscriber->currency ?? 'No CURRENCY found',
				'price_in_purchased_currency' => $subscriber->amount ?? 'No PRICE found',
			],
			'code' => 403
		];
	}
	// --------------------------------------

	public static function noDuplicationGate($userId, $tierId, $serviceId)
	{
		$subscriptionExists = SubscribedUserApp::userId($userId)->tierId($tierId)->subStatus("running")->active()->first();

		if ($subscriptionExists) {
			return ApiJsonReturnHelper::handle(false, 409, "You have already subscribed to this plan/package. Contact support for details!", [
				'data' => $subscriptionExists->only(
					['amount', 'payment_status', 'sub_status', 'duration', 'invoice_id', 'pdf_first', 'brand', 'last4', 'expiry']
				),
			]);
		}
		return null;
	}

}
/*
USE CASE-1:
	// ------------------------ SUBSCRIPTION GATE ------------------------
	if (strtolower($singlePlan?->audience) !== 'all') {
		$subscriptionGate = SubscriptionCheckerApp::validityGate($USER);
		if ($subscriptionGate['status'] !== true) {
			return response()->json($subscriptionGate, $subscriptionGate['code'] ?? 201);
		}
	}
	// ------------------------ SUBSCRIPTION GATE ------------------------

USE CASE-2:

*/