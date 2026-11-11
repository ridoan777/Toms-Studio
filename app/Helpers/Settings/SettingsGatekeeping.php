<?php

namespace App\Helpers\Settings;

use App\Helpers\ApiJsonReturnHelper;
use App\Helpers\Errors\ExceptionHandling;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class SettingsGatekeeping
{
	// --------------------------------------
	public static function unblockChecking(?User $user, ?Model $object)
	{
		if (!$user->status) {
			ExceptionHandling::bailout(409, "Your account is not active. The requested task is aborted. Contact support.", $object);
		}

		if ($object && !$object?->status) {
			ExceptionHandling::bailout(409, "The content/feature is not active. And the requested task is aborted. Try different content or contact support.");
		}

		return null;
	}

	// --------------------------------------

}
/*
This helper checks if the user | content | subscription Tier is blocked or not.

USE CASE-1:
if(!($existingSubscription = SettingsGatekeeping::unblockChecking($user->id, $project, $tier->id))){
	return $existingSubscription;
}
*/