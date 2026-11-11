<?php

namespace App\Domains\Lessons\Enums;

enum LessonDifficultyEnums: string
{
	case BEGINNER = 'beginner';
	case INTERMEDIATE = 'intermediate';
	case ADVANCED = 'advanced';

	// ----------------- LABEL -----------------
	public function label(): string
	{
		return match($this) {
			self::BEGINNER => 'Beginner',
			self::INTERMEDIATE => 'Intermediate',
			self::ADVANCED => 'Advanced',
		};
	}

	// ----------------- OPTIONS -----------------
	public static function options(): array
	{
		return array_map(fn($case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
	}

	// ----------------- VALUES -----------------
	public static function values(): array
	{
		return array_column(self::cases(), 'value');
	}
}