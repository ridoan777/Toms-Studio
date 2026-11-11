<?php

namespace App\Helpers\Notifications;
use App\Helpers\Errors\LoggerAccess;
use App\Models\System\Settings\OptionSiteSetup;
use App\Models\System\Settings\UserAppPreference;
use App\Models\Users\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class PushNotificationHelper
{
	public static $forcedPush = false;
	public static function handle(?bool $isMultiple = false, $notification, ?User $singleUser = null, ?bool $forcedPush = false)
	{
		if (!self::isEnabled())
			return;

		if ($forcedPush && self::$forcedPush === false)
			self::$forcedPush = $forcedPush;

		if ($isMultiple) {
			return self::allUserPush($notification);
		} else {
			return self::singleUserPush($notification, $singleUser);
		}
	}
	// ------------------------------------------------------------------------------------	

	public static function isEnabled(): bool
	{
		$push = OptionSiteSetup::where('type', 'notifications')->where('name', 'push_notification')->value('value');

		return (int)$push === 1;
	}
	// ------------------------------------------------------------------------------------	

	public static function isAllowedByUser($USER): bool
	{
		$userPreferredSetting = UserAppPreference::where('user_id', $USER->id)->pushNotification(1)->first();

		return $userPreferredSetting ? true : false;
	}
	// ------------------------------------------------------------------------------------	

	public static function allUserPush($notification)
	{
		User::whereNotNull('fcm_token')->chunk(100, function ($users) use ($notification) {
			foreach ($users as $recipient) {
				try {
					/** @var \App\Models\Users\User $recipient */
					if (self::isAllowedByUser($recipient) || self::$forcedPush) {
						$recipient->notify($notification);
					}
				} catch (Throwable $e) {
					Log::error('FCM send failed for all users', ['user_id' => $recipient->id ?? "N/A user_id", 'error' => $e->getMessage()]);
				}
			}
		});

		return true;
	}
	// -------------------------------------------------------------------------------

	public static function selectedUsersPush($notification, $users, $forcedPush)
	{
		if (!self::isEnabled())
			return;

		if ($forcedPush && self::$forcedPush === false)
			self::$forcedPush = $forcedPush;

		if (!$users || $users->isEmpty()) {
			LoggerAccess::showLog(['local', 'staging'], 'info', "Selected users list is empty", [$users]);
			return;
		}

		$users->unique('id')->chunk(100)->each(function ($chunk) use ($notification) {
			foreach ($chunk as $recipient) {
				if (!$recipient->fcm_token)
					continue;

				try {
					/** @var \App\Models\Users\User $recipient */
					if (self::isAllowedByUser($recipient) || self::$forcedPush) {
						$recipient->notify($notification);
					}
				} catch (Throwable $e) {
					Log::error('FCM send failed for selected users', ['user_id' => $recipient->id ?? "N/A user_id", 'error' => $e->getMessage()]);
				}
			}
		});

		return true;
	}
	// ------------------------------------------------------------------------------------	

	public static function singleUserPush($notification, User $singleUser)
	{
		if (!$singleUser || !$singleUser->fcm_token) {
			LoggerAccess::showLog(['local', 'staging'], 'info', "Single user FCM token not found", [$singleUser?->email ?? null]);
			return;
		}

		try {
			if (self::isAllowedByUser($singleUser) || self::$forcedPush) {
				$singleUser->notify($notification);
			}
		} catch (Throwable $e) {
			Log::error('FCM send failed for single user', ['user_name' => $singleUser?->name ?? "N/A name", 'error' => $e->getMessage()]);
		}
		return true;
	}
}
/*
	// ------------------------- PUSH NOTIFTCATION -------------------------
	USE CASE-1:
	PushNotificationHelper::handle(true, new LessonSavePushNotify(false, $lesson), null);

	USE CASE-2:

	$users = User::whereIn('id', [$user1Id, $user2Id])->get();
	PushNotificationHelper::selectedUsersPush(new PushNotification($data), $users);

	// ------------------------- PUSH NOTIFTCATION -------------------------
*/