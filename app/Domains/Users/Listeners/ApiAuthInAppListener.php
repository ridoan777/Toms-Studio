<?php

namespace App\Domains\Users\Listeners;

use App\Domains\Users\Events\ApiAuthNotifyEvent;
use App\Enums\NotifyChannelEnums;
use App\Notifications\InAppNotifications\ApiAuthInAppNotify;
use App\Notifications\PushNotifications\ApiAuthPushNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ApiAuthInAppListener
{
    // called from App\Domains\Users\Events\ApiAuthNotifyEvent
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
    public function handle(ApiAuthNotifyEvent $event): void
    {
        if (!in_array(NotifyChannelEnums::IN_APP, $event->channels)) {
            return;
        }

        $forced = in_array(NotifyChannelEnums::IN_APP, $event->force);

        $notifier = new ApiAuthInAppNotify($event->targetUser, $event->actor, $forced, $event->targetUser);

        $notifier->handle($event->actionFlag);
    }
}