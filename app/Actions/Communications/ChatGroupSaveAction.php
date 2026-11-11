<?php

namespace App\Actions\Communications;

use App\Models\Communication\Chatting\{ChatGroup, ChatGroupMember};

class ChatGroupSaveAction
{
	public function execute(array $validated, $chat_group_uid, $CREATOR): array
	{
		$newOwner = null;

		$newGroup = ChatGroup::updateOrCreate(
			[
				'id' => $validated['id'] ?? null,
			],
			[
				'chat_group_uid' => $chat_group_uid,
				'creator_id' => $CREATOR->id,
				'title' => $validated['title'],
				'description' => $validated['description'] ?? null,
				'image' => $validated['image']['path'] ?? null,
			]
		);
		$newOwner = ChatGroupMember::updateOrCreate(
			[
				'chat_group_id' => $newGroup->id,
				'member_id' => $newGroup->creator_id,
			],
			[
				'adder_id' => $CREATOR->id,
				'member_role' => "room_admin",
			]
		);
		$newOwner->load('chatGroupMemberRelatingBackTo_User:id,user_uid,name,email,avatar,status');
		$newOwner->owner_details = $newOwner->chatGroupMemberRelatingBackTo_User;
		unset($newOwner->chatGroupMemberRelatingBackTo_User);

		return [
			'group_details' => $newGroup,
			'owner_details' => $newOwner,
		];
	}
}

/*
USE CASE:
	use App\Actions\Billings\SubscriptionTierSaveAction;
	public function store(TierValidationRequest $request, SubscriptionTierSaveAction $action)
	{
		... ... ...
		$tier = $action->execute($validated);
	}
*/