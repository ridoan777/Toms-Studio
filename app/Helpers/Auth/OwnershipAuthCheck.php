<?php

namespace App\Helpers\Auth;

use App\Helpers\Errors\ExceptionHandling;
use App\Models\Users\User;

class OwnershipAuthCheck
{
	public static function ownerVsOwnerWithPermission(?User $user = null, ?string $permission = null, int $creatorId, int $claimantId)
	{
		if (!$user) {
			ExceptionHandling::bailout(401, "Unauthenticated user");
		}

		if ($user->hasRole('super_admin')) {
			return true;
		}

		if (!$user->can($permission)) {
			ExceptionHandling::bailout(403, "Unauthorized! Your role does not have permission to proceed & perform this action.");
		}
		if ((int)($creatorId) !== (int)($claimantId)) {
			ExceptionHandling::bailout(403, "Unauthorized! You are not the owner of this section/content.");
		}
		return true;
	}

	public static function ownerVsOwner(int $creatorId, int $claimantId, $messageTail = null)
	{
		$tail = $messageTail ?? 'section/content';
		if ((int)($creatorId) !== (int)($claimantId)) {
			ExceptionHandling::bailout(403, "Unauthorized! You are not the owner of this {$tail}");
		}
		return true;
	}

	public static function ownerInList(array $ownerIds, int $claimantId, $messageTail = null)
	{
		$tail = $messageTail ?? 'section/content';

		$normalized = array_map('intval', $ownerIds);

		if (!in_array((int)$claimantId, $normalized, true)) {
			ExceptionHandling::bailout(403, "Unauthorized! You are not allowed to access this {$tail}");
		}

		return true;
	}
}
/*
This helper checks if the respective content is owned by the requested user. Just call it as follows. No if-else is needed.
	OwnershipAuthCheck::ownerVsOwner($creator, $user->id, "project");
	OwnershipAuthCheck::ownerInList([$creator_1->id, $creator_2->id], $user->id, "project");
*/