<?php

namespace App\Domains\Users\Events;

use App\Domains\Users\Enums\ApiAuthEnums;
use App\Enums\NotifyChannelEnums;
use App\Models\Users\User;
use Illuminate\Broadcasting\{Channel, InteractsWithSockets, PrivateChannel};
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApiAuthNotifyEvent
{
	use Dispatchable, InteractsWithSockets, SerializesModels;
	
	public function __construct(
		public User $targetUser,
		public ?string $objectName = null,
		public User $actor,
		public ApiAuthEnums $actionFlag,
		public array $channels = [NotifyChannelEnums::PUSH, NotifyChannelEnums::IN_APP],
		public array $force = [] // [NotifyChannelEnums::PUSH, NotifyChannelEnums::IN_APP]
	) {}
}

// -------------------- NOTIFICATION EVENTS --------------------
// $actionFlag = ApiAuthEnums::LOGIN;

// event(new ApiAuthNotifyEvent($user, null, $USER, $actionFlag, [NotifyChannelEnums::PUSH, NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP]));
// -------------------- NOTIFICATION EVENTS --------------------