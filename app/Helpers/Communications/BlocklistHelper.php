<?php

namespace App\Helpers\Communications;

use App\Models\Communication\Chatting\ChatBlocklist;

class BlocklistHelper
{
	// --------------------------------------
	public static function checkBlockedUser(int $userA, int $userB): bool
	{
		$composite = $userA .'-'. $userB;

		return ChatBlocklist::where('is_blocked', 1)->status(1)
			->where(function ($q) use ($composite){
				$q->where('composite_a', $composite)->orWhere('composite_b', $composite);
		})->exists();
	}

	// --------------------------------------

}