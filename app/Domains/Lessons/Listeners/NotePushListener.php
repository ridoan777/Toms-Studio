<?php

namespace App\Domains\Lessons\Listeners;

use App\Domains\Lessons\Events\NoteNotifyEvent;
use App\Enums\NotifyChannelEnums;
use App\Helpers\Notifications\PushNotificationHelper;
use App\Models\Users\User;
use App\Notifications\PushNotifications\NotePushNotify;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotePushListener
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
        if (!in_array(NotifyChannelEnums::PUSH, $event->channels)) {
            return;
        }

        $forced = in_array(NotifyChannelEnums::PUSH, $event->force);

        $receivers = User::whereIn('id', $event->targetUsers)->get();

        PushNotificationHelper::selectedUsersPush(new NotePushNotify(false, $event->objectName, $event->actor, $event->actionFlag), $receivers, $forced);
    }
}
