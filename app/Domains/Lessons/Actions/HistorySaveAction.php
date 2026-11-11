<?php

namespace App\Domains\Lessons\Actions;

use App\Helpers\Errors\ExceptionHandling;
use App\Models\Workflows\Lessons\LessonHistory;

class HistorySaveAction
{
	public function execute(array $validated, ?LessonHistory $existingHistory, $type, $class, $CURRENT_USER, $LESSON_HISTORY_UID, $isNewRecord)
	{
		# Due to proprietary secrecy, code that contained business rules were removed
	}
}