<?php

namespace App\Notifications\InAppNotifications;

use App\Enums\CRUDActionEnums;
use App\Helpers\Notifications\InAppNotificationHelper;
use App\Models\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;

class LessonInAppNotifier
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 */
	public $recipients = null;

	public function __construct(
		public $lesson = null,
		public $actor = null,
		public $forcedShow = false,
		public $receivers = null,
	) {
		$this->recipients = match (true) {
			$receivers instanceof Collection => $receivers,
			is_array($receivers) => User::whereIn('id', $receivers)->get(),
			default => collect()
		};
	}

	public function handle(?CRUDActionEnums $actionFlag = CRUDActionEnums::UPDATE)
	{
		switch ($actionFlag) {
			case CRUDActionEnums::CREATE:
				return $this->create();

			case CRUDActionEnums::UPDATE:
				return $this->update();

			case CRUDActionEnums::ACTIVATE:
			case CRUDActionEnums::DEACTIVATE:
				return $this->status($actionFlag);

			case CRUDActionEnums::DELETE:
				return $this->delete();
		}
	}
	// ----------------------------------------------------

	private function create()
	{
		$this->recipients->unique('id')->chunk(100)->each(function ($chunk) {
			foreach ($chunk as $recipient) {
				$title = "New lesson added";
				$message = "You have been added to the lesson '{$this?->lesson?->title}' by {$this?->actor?->name}. Go check this out!";

				$this->callNotifyHelper($recipient, 'info', 'normal', $title, $message, 'backend_lesson_show');
			}
		});
	}
	// ----------------------------------------------------

	private function update()
	{
		$this->recipients->unique('id')->chunk(100)->each(function ($chunk) {
			foreach ($chunk as $recipient) {
				$title = "Lesson updated";
				$message = "Your lesson '{$this?->lesson?->title}' has been modifed by {$this?->actor?->name} ({$this?->actor?->user_role}). Go check it out!";

				$this->callNotifyHelper($recipient, 'info', 'alert', $title, $message, 'backend_lesson_show');
			}
		});
	}
	// ----------------------------------------------------

	private function status($actionFlag)
	{
		$this->recipients->unique('id')->chunk(100)->each(function ($chunk) use ($actionFlag) {
			foreach ($chunk as $recipient) {
				$title = "Lesson {$actionFlag->value}";
				$message = "Your lesson '{$this?->lesson?->title}' has been {$actionFlag->value} by {$this?->actor?->name} ({$this?->actor?->user_role}).";

				$this->callNotifyHelper($recipient, 'info', 'alert', $title, $message, 'backend_lesson_show');
			}
		});
	}
	// ----------------------------------------------------

	private function delete()
	{
		$this->recipients->unique('id')->chunk(100)->each(function ($chunk) {
			foreach ($chunk as $recipient) {
				$title = "Lesson deleted";
				$message = "Your lesson '{$this?->lesson?->title}' has been deleted by {$this?->actor?->name} ({$this?->actor?->user_role}).";

				$this->callNotifyHelper($recipient, 'info', 'critical', $title, $message, 'backend_lesson_show');
			}
		});
	}
	// ----------------------------------------------------

	private function callNotifyHelper($recipient, $type, $severity, $title, $message, $route)
	{
		return InAppNotificationHelper::createInAppNotify(
			$recipient,
			'web_lesson_store',
			$type,
			$severity,
			$title,
			$message,
			$this?->actor?->name,
			$this?->actor?->avatar_url,
			$route ? route('backend_lesson_show', $this->lesson->id) : null,
			"visit",
			$this?->forcedShow,
		);
	}
}
