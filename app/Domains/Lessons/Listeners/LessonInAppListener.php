<?php

namespace App\Domains\Lessons\Listeners;

use App\Domains\Lessons\Events\LessonNotifyEvent;
use App\Enums\NotifyChannelEnums;
use App\Notifications\InAppNotifications\LessonInAppNotifier;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LessonInAppListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LessonNotifyEvent $event): void
    {
        if (!in_array(NotifyChannelEnums::IN_APP, $event->channels)) {
            return;
        }

        $forced = in_array(NotifyChannelEnums::IN_APP, $event->force);

        $notifier = new LessonInAppNotifier($event->lesson, $event->actor, $forced, $event->targetUsers);

        $notifier->handle($event->actionFlag);
    }
}
