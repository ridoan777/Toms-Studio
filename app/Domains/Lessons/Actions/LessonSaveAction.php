<?php

namespace App\Domains\Lessons\Actions;

use App\Enums\LessonCategoryEnums;
use App\Models\Workflows\Lessons\Lesson;

class LessonSaveAction
{
	public function execute(array $validated, ?Lesson $existingLesson, $CURRENT_USER, $LESSON_UID, $isNewRecord)
	{
		# Due to proprietary secrecy, code that contained business rules were removed
	}
}