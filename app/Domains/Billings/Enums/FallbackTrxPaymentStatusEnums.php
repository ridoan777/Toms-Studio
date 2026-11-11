<?php

namespace App\Domains\Billings\Enums;

// we are retrieving payment_status from Stripe. This enum is working only as a fallback or payment amount mismatch.

enum FallbackTrxPaymentStatusEnums: string
{
	case PAID = 'paid';
	case UNPAID = 'unpaid';
	case PARTIAL = 'partial';
	case FAILED = 'failed';

	// ----------------- LABEL -----------------
	public function label(): string
	{
		return match($this) {
			self::PAID => 'Paid',
			self::UNPAID => 'Unpaid',
			self::PARTIAL => 'Partial',
			self::FAILED => 'Failed',
		};
	}
}