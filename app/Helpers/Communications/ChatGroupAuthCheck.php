<?php

namespace App\Helpers\Communications;

use App\Helpers\ApiJsonReturnHelper;
use App\Helpers\Errors\ExceptionHandling;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Models\Communication\Chatting\{ChatBlocklist, ChatGroupMember};

class ChatGroupAuthCheck
{
	// --------------------------------------
	public static function isUserMember(int $groupId, $currentMember, $members = null, $allowedTotalMembers = null)
	{
		if (!ChatGroupMember::groupId($groupId)->member($currentMember)->exists()) {
			ExceptionHandling::bailout(403, "Unauthorized! You are not a member & don't have permission to perform this action in this group!");
		}
		if (!empty($allowedTotalMembers)) {
			self::canAddNewMember($groupId, $members, $allowedTotalMembers);
		}
		return null;
	}
	// --------------------------------------

	public static function canAddNewMember($groupId, $members, $allowedTotalMembers)
	{
		$totalOldMembers = ChatGroupMember::groupId($groupId)->count();
		$totalNewMembers = collect($members)->unique()->count();

		if (($totalOldMembers + $totalNewMembers) > $allowedTotalMembers) {
			ExceptionHandling::bailout(403, "Aborting! Adding these member(s) will exceed total limit of : {$allowedTotalMembers}.");
		}
	}
	// --------------------------------------

	public static function isMemberRemovable($group, $user = null, $targetMemberId = null)
	{
		$userId = $user->id;

		self::isUserMember($group->id, $userId);

		$actingMember = ChatGroupMember::groupId($group->id)->member($userId)->first();
		$targetMember = ChatGroupMember::groupId($group->id)->member($targetMemberId)->first();

		if (empty($targetMember)) {
			ExceptionHandling::bailout(404, "The requested user either doesn't belong to this group anymore or has been removed already!");
		}

		// if target user is an admin/not
		$cannotRemove =
			(int)$group->creator_id === (int)$targetMemberId ||
			$targetMember->member_role === 'room_admin';

		if ($cannotRemove) {
			ExceptionHandling::bailout(403, "Unauthorized! You cannot remove group admins");
		}

		// checking user has the role or is an adder or he himself
		$canRemove =
			((int)$group->creator_id === (int)$userId ||
			(int)$targetMember->adder_id === (int)$userId ||
			(int)$targetMemberId === (int)$userId ||
			in_array($actingMember->member_role, ['moderator', 'room_admin'], true)) && $targetMember->member_role !== 'room_admin';

		if (!$canRemove) {
			ExceptionHandling::bailout(403, "Unauthorized! Only Admin, Moderator & the person who added can remove a member");
		}
		return $targetMember;
	}
	// --------------------------------------
}

/*
	Just call it. No if-else is needed. If OK, next codes will run, else will abort.

	USE CASE-1: No member check
		ChatGroupAuthCheck::isUserMember($USER, $validated['group_id'], $member, null, null);

	USE CASE-2: With member check
		ChatGroupAuthCheck::isUserMember($USER, $validated['group_id'], $member, $validated['member_id'], $this->MAX_GROUP_MEMBERS);

	USER CASE-2: REMOVING/LEAVING MEMBER:
		$groupMember = ChatGroupAuthCheck::isMemberRemovable($group, $USER, $member->id);
*/