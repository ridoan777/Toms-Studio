<?php

namespace App\Notifications\InAppNotifications;

use App\Helpers\Errors\LoggerAccess;
use App\Helpers\Notifications\InAppNotificationHelper;
use App\Models\Users\User;
use Illuminate\Bus\Queueable;

class ProjectInAppNotifier
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 */
	public function __construct(
		public $project = null,
		public $actor = null,
		public $forcedShow = false,
	) {
	}

	public function create(?User $recipient = null)
	{
		LoggerAccess::showLog(['local', 'staging'], 'info', "In-app:create has reached", [$recipient]);

		return InAppNotificationHelper::createInAppNotify(
			$recipient,
			'web_project_store',
			'update',
			'info',
			"A new project '{$this?->project?->title}' has been created by {$this?->actor?->name}.",
			$this?->actor?->name,
			$this?->actor?->avatar_url,
			route('backend_project_show', $this->project->id),
			"visit",
			$this?->forcedShow,
		);
	}

	public function update(?User $recipient = null)
	{
		LoggerAccess::showLog(['local', 'staging'], 'info', "In-app:update has reached", [$recipient]);

		return InAppNotificationHelper::createInAppNotify(
			$recipient,
			'web_project_store',
			'update',
			'info',
			"The project '{$this?->project?->title}' has been modifed by {$this?->actor?->name}.",
			$this?->actor?->name,
			$this?->actor?->avatar_url,
			route('backend_project_show', $this->project->id),
			"visit",
			$this?->forcedShow,
		);
	}
}
