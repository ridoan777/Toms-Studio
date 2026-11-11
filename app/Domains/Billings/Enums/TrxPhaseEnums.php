<?php

namespace App\Domains\Billings\Enums;

enum TrxPhaseEnums: string
{
	case INITIATED = 'initiated';
	case COMPLETED = 'completed';
	case CANCELLED = 'cancelled';
	case FAILED = 'failed';

	// ----------------- LABEL -----------------
	public function label(): string
	{
		return match($this) {
			self::INITIATED => 'Initiated',
			self::COMPLETED => 'Completed',
			self::CANCELLED => 'Cancelled',
			self::FAILED => 'Failed',
		};
	}
}