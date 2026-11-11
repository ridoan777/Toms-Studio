<?php

namespace App\Domains\Users\Listeners;

use App\Domains\Users\Events\ApiAuthNotifyEvent;
use App\Enums\NotifyChannelEnums;
use App\Helpers\Notifications\PushNotificationHelper;
use App\Notifications\PushNotifications\ApiAuthPushNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ApiAuthPushListener
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
        if (!in_array(NotifyChannelEnums::PUSH, $event->channels)) {
            return;
        }

        $forced = in_array(NotifyChannelEnums::PUSH, $event->force);

        PushNotificationHelper::handle(false, new ApiAuthPushNotify(false, $event->actor, $event->actionFlag), $event->targetUser, $forced);
    }
}