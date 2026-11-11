<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class PushNotification extends Notification
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 */
	public function __construct(
		public string $title,
		public string $body,
		public array $data = []
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
		if (app()->environment('local')) {
			Log::info("Reached to the PushNotification.php");
		}
		return FcmMessage::create()
			->notification(
				FcmNotification::create()
					->title($this->title)
					->body($this->body)
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


	/*
	public function toFcm(object $notifiable): FcmMessage
	{
		return FcmMessage::create()
			->notification(
				FcmNotification::create()
					->title($this->title ?? "Title from PushNotification")
					->body($this->body ?? "Body from PushNotification!")
			)
			->data([
				'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
				'timestamp' => now()->toIso8601String(),
			]);
	}
	*/
}
