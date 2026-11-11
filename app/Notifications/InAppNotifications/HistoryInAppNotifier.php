<?php

namespace App\Notifications\InAppNotifications;

use App\Enums\CRUDActionEnums;
use App\Helpers\Notifications\InAppNotificationHelper;
use App\Models\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;

class HistoryInAppNotifier
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 */
	public $recipients = null;
	public $lesson = null;

	public function __construct(
		public $history = null,
		public $actor = null,
		public $forcedShow = false,
		public $receivers = null,
	) {
		$this->recipients = match (true) {
			$receivers instanceof Collection => $receivers,
			is_array($receivers) => User::whereIn('id', $receivers)->get(),
			default => collect()
		};

		$this->lesson = null;

		if ($this?->history?->lesson_id) {
			$this->lesson = $this?->history?->scheduleRelatingBackTo_Lesson;
		} elseif ($this?->history?->group_id) {
			$this->lesson = $this?->history?->scheduleRelatingBackTo_LessonGroup;
		}
	}

	public function handle(?CRUDActionEnums $actionFlag = CRUDActionEnums::UPDATE)
	{
		switch ($actionFlag) {
			case CRUDActionEnums::CREATE:
				return $this->create();

			case CRUDActionEnums::UPDATE:
				return $this->update();

			case CRUDActionEnums::DELETE:
				return $this->delete();
		}
	}
	// ----------------------------------------------------

	private function create()
	{
		$this->recipients->unique('id')->chunk(100)->each(function ($chunk) {
			foreach ($chunk as $recipient) {
				$title = "Class history added";
				$message = "{$this?->actor?->name} has marked the lesson '{$this?->lesson?->title}' as '{$this?->history?->phase}' for {$this?->history?->final_date}!";

				$this->callNotifyHelper($recipient, 'info', 'normal', $title, $message, 'backend_lesson_histories_show');
			}
		});
	}
	// ----------------------------------------------------

	private function update()
	{
		$this->recipients->unique('id')->chunk(100)->each(function ($chunk) {
			foreach ($chunk as $recipient) {
				$title = "Class history updated";
				if (!empty($this?->history?->final_date)) {
					$message = "{$this?->actor?->name} has updated the lesson '{$this?->lesson?->title}' as '{$this?->history?->phase}' for {$this?->history?->final_date}!";
				} else {
					$message = "{$this?->actor?->name} has updated the lesson '{$this?->lesson?->title}' as '{$this?->history?->phase}'!";
				}

				$this->callNotifyHelper($recipient, 'info', 'normal', $title, $message, 'backend_lesson_histories_show');
			}
		});
	}
	// ----------------------------------------------------

	private function delete()
	{
		$this->recipients->unique('id')->chunk(100)->each(function ($chunk) {
			foreach ($chunk as $recipient) {
				$title = "Class history record deleted";
				$message = "{$this?->actor?->name} has deleted the class history for '{$this?->lesson?->title}'!";

				$this->callNotifyHelper($recipient, 'delete', 'critical', $title, $message, 'backend_lesson_histories_show');
			}
		});
	}
	// ----------------------------------------------------

	private function callNotifyHelper($recipient, $type, $severity, $title, $message, $route)
	{
		return InAppNotificationHelper::createInAppNotify(
			$recipient,
			'web_lesson_history',
			$type,
			$severity,
			$title,
			$message,
			$this?->actor?->name,
			$this?->actor?->avatar_url,
			$route ? route('backend_lesson_histories_show', $this->history->id) : null,
			"visit",
			$this?->forcedShow,
		);
	}
}
