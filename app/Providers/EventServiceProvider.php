<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskFailed;

class EventServiceProvider extends ServiceProvider
{
	protected $listen = [
			// ScheduledTaskStarting::class => [
			// 	\App\Listeners\ScheduledTaskStartingListener::class,
			// ],
			// ScheduledTaskFinished::class => [
			// 	\App\Listeners\ScheduledTaskFinishedListener::class,
			// ],
		// ScheduledTaskSkipped::class => [
		// 	\App\Listeners\ScheduledTaskSkippedListener::class,
		// ],
		// ScheduledTaskFailed::class => [
		// 	\App\Listeners\ScheduledTaskFailedListener::class,
		// ],
		\App\Domains\Users\Events\ApiAuthNotifyEvent::class => [
			\App\Domains\Users\Listeners\ApiAuthPushListener::class,
			\App\Domains\Users\Listeners\ApiAuthInAppListener::class,
		],
		\App\Domains\Lessons\Events\LessonNotifyEvent::class => [
			\App\Domains\Lessons\Listeners\LessonPushListener::class,
			\App\Domains\Lessons\Listeners\LessonInAppListener::class,
		],
		\App\Domains\Lessons\Events\NoteNotifyEvent::class => [
			\App\Domains\Lessons\Listeners\NotePushListener::class,
			\App\Domains\Lessons\Listeners\NoteInAppListener::class,
		],
		\App\Domains\Lessons\Events\HistoryNotifyEvent::class => [
			\App\Domains\Lessons\Listeners\HistoryPushListener::class,
			\App\Domains\Lessons\Listeners\HistoryInAppListener::class,
		],
	];
}