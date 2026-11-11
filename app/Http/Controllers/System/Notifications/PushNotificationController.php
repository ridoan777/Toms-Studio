<?php

namespace App\Http\Controllers\System\Notifications;

use App\Helpers\Errors\ExceptionHandling;
use App\Http\Controllers\Controller;
use App\Models\System\Settings\OptionSiteSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log};
use Throwable;

class PushNotificationController extends Controller
{
	public function saveFcmToken(Request $request)
	{
		$multiPush = OptionSiteSetup::where('type', "notifications")->where('name', "multi_push_notification")->first();
		
		if ((int) ($multiPush?->value) === 1) {
			return $this->saveMultiDeviceFcmToken($request);
		} else if ((int) ($multiPush?->value) === 0) {
			return $this->saveSingleDeviceFcmToken($request);
		} else {
			return response()->json([
				'status' => false,
				'message' => "Invalid push notification device access. Contact admin.",
				'code' => 500
			]);
		}
	}

	private function saveSingleDeviceFcmToken($request)
	{
		try {
			// Log::info('FCM route hit from SINGLE Class, and by:', ['user_id' => optional($request->user())->id]);
			$request->validate(['token' => 'required|string']);
			$user = $request->user();

			if (!$user) {
				return response()->json(['error' => 'Unauthenticated'], 401);
			}

			if ($user->fcm_token !== $request->token) {
				$user->update(['fcm_token' => $request->token]);
			}

			$user->update(['fcm_token' => $request->token]);

			return response()->json([
				'status' => true,
				'message' => 'Token saved',
				'code' => 201
			]);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('saving FCM token', $e);
		}
	}

	private function saveMultiDeviceFcmToken($request)
	{
		// Log::info('FCM route hit from MULTI Class, and by:', ['user_id' => optional($request->user())->id]);
		try {
			$request->validate([
				'token' => 'required|string'
			]);

			$user = $request->user();

			if (!$user) {
				return response()->json([
					'status' => false,
					'error' => 'User Unauthenticated',
					'code' => 401,
				], 401);
			}

			$newToken = $request->token;

			$tokens = $user->fcm_token ?? [];

			if (is_string($tokens) && $tokens !== '') {
				$tokens = [$tokens];
			}

			if (!is_array($tokens)) {
				$tokens = [];
			}

			// Preventing duplicates
			if (!in_array($newToken, $tokens)) {
				$tokens[] = $newToken;
			}

			// limiting tokens for last 5 devices
			$tokens = array_slice(array_values(array_unique($tokens)), -5);

			$user->update([
				'fcm_token' => $tokens,
			]);

			// Log::info('FCM token saved', ['user_id' => $user->id,'tokens' => $tokens]);

			return response()->json([
				'status' => true,
				'message' => 'Token saved',
				'tokens' => $tokens,
				'code' => 201
			], 201);

		} catch (Throwable $e) {
			return ExceptionHandling::handle('saving FCM token', $e);
		}
	}
}
