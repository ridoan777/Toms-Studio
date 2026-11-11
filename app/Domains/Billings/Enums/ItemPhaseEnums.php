<?php

namespace App\Domains\Billings\Enums;

enum ItemPhaseEnums: string
{
	case CREATED = 'created';
	case SYNCED = 'synced';
	case CANCELLED = 'cancelled';
	case FAILED = 'failed';

	// ----------------- LABEL -----------------
	public function label(): string
	{
		return match($this) {
			self::CREATED => 'Created',
			self::SYNCED => 'Synced',
			self::CANCELLED => 'Cancelled',
			self::FAILED => 'Failed',
		};
	}
}