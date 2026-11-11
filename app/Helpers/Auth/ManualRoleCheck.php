<?php

namespace App\Helpers\Auth;

use App\Models\Users\User;

class ManualRoleCheck
{
	private static $modifiedFileName = '';
	private static $DBFirendlyFileName = '';

	// --------------------------------------
	public static function authorization($userId, $roleWhoCanDoThis = 'project_manager', $roleWhoCannotDoThis = 'site_manager')
	{
		$user = User::find($userId);

		if (!$user) {
			return false;
		}

		if (strtolower($user->user_role) !== strtolower($roleWhoCanDoThis)) {
			return false;
		}
		return true;

	}

	// --------------------------------------

}
