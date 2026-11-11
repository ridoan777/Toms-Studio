<?php

namespace App\Notifications\PushNotifications;

use App\Domains\Users\Enums\ApiAuthEnums;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ApiAuthPushNotify extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 */
	public function __construct(
		public ?bool $isMultiple = false,	//single, multiple [single user or multi]
		public ?User $actor,
		public ?ApiAuthEnums $actionFlag,
	) {
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @return array<int, string>
	 */
	public function via(object $notifiable): array
	{
		return [FcmChannel::class];
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(object $notifiable): array
	{
		return [
			//
		];
	}

	public function toFcm(object $notifiable): FcmMessage
	{
		// LoggerAccess::showLog(['local', 'staging'], 'info', "FCM push has reached", [$notifiable]);
		$pushTitle = "Hi! " . ($notifiable?->name ?? "user");
		
		$action = $this->actionFlag?->value ?? 'action';

		$pushBody = $this->actionFlag === ApiAuthEnums::LOGIN
			? "Your account was logged-in at " . Carbon::now('UTC')->setTimezone($notifiable->timezone ?? 'UTC')->toDayDateTimeString()
			: "A/An " . str_replace('_', ' ', $action) . " attempt was made for your account at " . Carbon::now('UTC')->setTimezone($notifiable->timezone ?? 'UTC')->toDayDateTimeString();


		return FcmMessage::create()
			->notification(
				FcmNotification::create()
					->title($pushTitle)
					->body($pushBody)
			)
			->data([
				'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
				'sound' => 'default',
				'priority' => 'high',
				'timestamp' => now()->toIso8601String(),
			])
			// ->token('token')
			->token($notifiable->fcm_token)
			->topic('topic');
	}
}
/*
	// ------------------------- PUSH NOTIFTCATION -------------------------
		PushNotificationHelper::singleUserPush(new ApiAuthPushNotify(false, $actor, $actionFlag), $targetUser);
	// ------------------------- PUSH NOTIFTCATION -------------------------
*/