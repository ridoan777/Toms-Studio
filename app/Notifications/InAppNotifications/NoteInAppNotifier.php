<?php

namespace App\Notifications\InAppNotifications;

use App\Enums\CRUDActionEnums;
use App\Helpers\Notifications\InAppNotificationHelper;
use App\Models\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;

class NoteInAppNotifier
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 */
	public $recipients = null;

	public function __construct(
		public $parentObject = null,
		public $objectName = null,
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
				$title = "New note added";
				$message = "{$this?->actor?->name} has added a lesson note for you. Note UID: {$this?->parentObject?->note_uid}. Go check this out!";

				$this->callNotifyHelper($recipient, 'info', 'normal', $title, $message, 'backend_lesson_show');
			}
		});
	}
	// ----------------------------------------------------

	private function update()
	{
		$this->recipients->unique('id')->chunk(100)->each(function ($chunk) {
			foreach ($chunk as $recipient) {
				$title = "Note updated";
				$message = "Your lesson note '{$this?->parentObject?->title}' has been modifed by {$this?->actor?->name}. Go check it out!";

				$this->callNotifyHelper($recipient, 'info', 'alert', $title, $message, 'backend_lesson_show');
			}
		});
	}
	// ----------------------------------------------------

	private function status($actionFlag)
	{
		$this->recipients->unique('id')->chunk(100)->each(function ($chunk) use($actionFlag) {
			foreach ($chunk as $recipient) {
				$title = "Note {$actionFlag->value}";
				$message = "Your lesson note '{$this?->parentObject?->title}' has been {$actionFlag->value} by {$this?->actor?->name}.";

				$this->callNotifyHelper($recipient, 'info', 'alert', $title, $message, 'backend_lesson_show');
			}
		});
	}
	// ----------------------------------------------------

	private function delete()
	{
		$this->recipients->unique('id')->chunk(100)->each(function ($chunk) {
			foreach ($chunk as $recipient) {
				$title = "Note deleted";
				$message = "Your lesson note '{$this?->objectName}' has been deleted by {$this?->actor?->name}.";

				$this->callNotifyHelper($recipient, 'info', 'critical', $title, $message, 'backend_lesson_show');
			}
		});
	}
	// ----------------------------------------------------

	private function callNotifyHelper($recipient, $type, $severity, $title, $message, $route)
	{
		return InAppNotificationHelper::createInAppNotify(
			$recipient,
			'web_lesson_note_store',
			$type,
			$severity,
			$title,
			$message,
			$this?->actor?->name,
			$this?->actor?->avatar_url,
			($route && $this->parentObject) ? route('backend_lesson_note_show', $this->parentObject->id) : (route('backend_lesson_note_index') ?? null),
			"visit",
			$this?->forcedShow,
		);
	}
}
