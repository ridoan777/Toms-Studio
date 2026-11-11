<?php

namespace App\Helpers\Auth;

use App\Helpers\Errors\ExceptionHandling;
use App\Models\Users\UserAccess;

class UserAceesChecking
{
	// --------------------------------------
	public static function chatBan(int $userId)
	{
		$fetchUserAccess = UserAccess::userId($userId)->chatBan(true)->first();
		
		if ($fetchUserAccess && ($fetchUserAccess->ban_expiry->isFuture())) {
			ExceptionHandling::bailout(403, "Unauthorized! You are banned from the chatting due to chat violation. Wait till {$fetchUserAccess?->ban_expiry}");
		}
		return true;
	}

	// --------------------------------------

}
/*
 Just call it as follows. No if-else is needed.
 
	// ------------------- USER ACCESS CHECKING -------------------
	UserAceesChecking::chatBan($SENDER->id);
*/