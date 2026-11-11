<?php

namespace App\Domains\Lessons\Listeners;

use App\Domains\Lessons\Events\HistoryNotifyEvent;
use App\Enums\NotifyChannelEnums;
use App\Helpers\Notifications\PushNotificationHelper;
use App\Models\Users\User;
use App\Notifications\PushNotifications\HistorySavePushNotify;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HistoryPushListener
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
    public function handle(HistoryNotifyEvent $event): void
    {
        if (!in_array(NotifyChannelEnums::PUSH, $event->channels)) {
            return;
        }

        $forced = in_array(NotifyChannelEnums::PUSH, $event->force);

        $receivers = User::whereIn('id', $event->targetUsers)->get();

        PushNotificationHelper::selectedUsersPush(new HistorySavePushNotify(false, $event->history, $event->actor), $receivers, $forced);
    }
}
