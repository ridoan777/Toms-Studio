<?php

namespace App\Domains\Lessons\Enums;

enum LessonPhaseEnums: string
{
	case UPCOMING = 'upcoming';
	case IN_PROGRESS = 'in_progress';
	case MOVED = 'moved';
	case SCHEDULED = 'scheduled';
	case COMPLETED = 'completed';
	case NO_SHOW = 'no_show';
	case BELATED = 'belated';
	case CANCELLED = 'cancelled';

	// ----------------- LABEL -----------------
	public function label(): string
	{
		return match($this) {
			self::UPCOMING => 'Upcoming',
			self::IN_PROGRESS => 'In Progress',
			self::MOVED => 'Moved',
			self::SCHEDULED => 'Scheduled',
			self::COMPLETED => 'Completed',
			self::NO_SHOW => 'No Show',
			self::BELATED => 'Belated',
			self::CANCELLED => 'Cancelled',
		};
	}

	// ----------------- OPTIONS (FOR DROPDOWNS) -----------------
	public static function options(): array
	{
		return array_map(fn($case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
	}

	// ----------------- VALUES ONLY -----------------
	public static function values(): array
	{
		return array_column(self::cases(), 'value');
	}

	// ----------------- BUSINESS LOGIC -----------------
	public function isFinal(): bool
	{
		return in_array($this, [self::COMPLETED, self::CANCELLED, self::NO_SHOW]);
	}

	public function isActive(): bool
	{
		return in_array($this, [self::UPCOMING, self::IN_PROGRESS, self::SCHEDULED]);
	}
}