<?php

namespace App\Domains\Lessons\Listeners;

use App\Domains\Lessons\Events\NoteNotifyEvent;
use App\Enums\NotifyChannelEnums;
use App\Notifications\InAppNotifications\NoteInAppNotifier;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NoteInAppListener
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
    public function handle(NoteNotifyEvent $event): void
    {
        if (!in_array(NotifyChannelEnums::IN_APP, $event->channels)) {
            return;
        }

        $forced = in_array(NotifyChannelEnums::IN_APP, $event->force);

        $notifier = new NoteInAppNotifier($event?->parentObject, $event?->objectName, $event->actor, $forced, $event->targetUsers);

        $notifier->handle($event->actionFlag);
    }
}
