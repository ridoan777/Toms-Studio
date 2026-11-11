<?php

namespace App\Notifications\PushNotifications;

use App\Helpers\Errors\LoggerAccess;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ProjectSavePushNotify extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 */
	public function __construct(
		public $isMultiple = false,	//single, multiple [single user or multi]
		public $project,
		public $actor = null,
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
		$pushTitle = "Hi! {$notifiable?->name}";
		$pushBody = "All users have been notified for the change in project '{$this->project->title}'";

		if(!$this->isMultiple){
			$pushBody = "The project '{$this->project->title}' has been updated by '{$this->actor->name}'. Check it back!";
		}

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
