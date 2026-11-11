<?php

namespace App\Domains\Lessons\Actions;

use App\Models\Users\User;
use App\Models\Workflows\Lessons\LessonNote;

class NoteSaveAction
{
	public function execute(array $validated, ?LessonNote $existingNote = null, string $NOTE_UID, ?User $USER, bool $isNewRecord = true)
	{
		# Due to proprietary secrecy, code that contained business rules were removed
	}
}