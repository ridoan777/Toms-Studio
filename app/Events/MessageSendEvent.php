<?php

namespace App\Events;

use App\Models\Communication\Chatting\ChatMessage;
use Illuminate\Broadcasting\{Channel, InteractsWithSockets, PrivateChannel};
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSendEvent implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;
		// check app\Providers\BroadcastServiceProvider.php
	public function __construct(public ChatMessage $message)
	{
		//--------------- Add logging to verify event is being created
		Log::info('MessageSent event created', [
			'message_id' => $this->message->id,
			'sender_id' => $this->message->sender_id,
			'receiver_id' => $this->message->receiver_id,
		]);
	}

	public function broadcastOn(): array
	{
		$channels = [
			new PrivateChannel('chat.' . $this->message->sender_id),
			new PrivateChannel('chat.' . $this->message->receiver_id),
		];

		//--------------- Log the channels being broadcast to
		Log::info('Broadcasting to channels', [
			'channels' => [
				'chat.' . $this->message->sender_id,
				'chat.' . $this->message->receiver_id,
			]
		]);

		return $channels;
	}

	public function broadcastWith(): array
	{
		$data = [
			'message' => $this->message->load(['chatSenderRelationWith_user', 'chatReceiverRelationWith_user'])->toArray()
		];

		//--------------- Log the data being broadcast
		Log::info('Broadcasting data', ['data' => $data]);

		return $data;
	}

	public function broadcastAs(): string
	{
		return 'MessageSent';
	}
}
