<?php

namespace App\Helpers\PaymentHelpers;

use App\Models\Billings\Subscriptions\SubscribedUserWeb;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SubscriptionCheckerWeb
{
	// --------------------------------------
	public static function validityGate($tierId, $courseId)
	{
		// user must be logged in
		if (!Auth::check()) {
			return response()->json([
				'message' => 'Aborting! You must be logged in to access this feature.',
			], 401);
		}

		// user must be subscriber
		$getSubscribedUser = SubscribedUserWeb::where('user_id', Auth::user()->id)->where('tier_id', $tierId)->where('course_id', $courseId)->latest()->first();
		if (!$getSubscribedUser) {
			return response()->json([
				'message' => 'Aborting! You must subscribe/purchase this course to access this feature.',
			], 401);
		}

		// user must have subscription validity
		if ($getSubscribedUser->duration === 'lifetime') {
			$expiryDate = now()->addYears(100);
		} else {
			$expiryDate = Carbon::parse($getSubscribedUser->created_at)->addDays((int)$getSubscribedUser->duration);
		}

		if (now()->greaterThan($expiryDate)) {
			return response()->json([
				'message' => 'Your subscription has expired. Please renew to continue accessing this course.',
			], 403);
		}
		return null;
	}
	// --------------------------------------

	public static function noDuplicationGate($userId, $tierId, $serviceId)
	{
		$subscriptionExists = SubscribedUserWeb::userId($userId)->tierId($tierId)->serviceId($serviceId)->status(1)->first();

		if ($subscriptionExists) {
			return response()->json([
				'status' => false,
				'message' => "You have already subscribed to this plan/package. Contact support for details!",
				'data' => $subscriptionExists->only(
					['amount', 'payment_status', 'sub_status', 'duration', 'invoice_id', 'pdf_first', 'brand', 'last4', 'expiry']
				),
				'code' => 409
			], 409);
		}
		return null;
	}

}
