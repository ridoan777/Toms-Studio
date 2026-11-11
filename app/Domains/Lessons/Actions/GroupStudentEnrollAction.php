<?php

namespace App\Domains\Lessons\Actions;

use App\Models\Workflows\Lessons\{LessonGroup, GroupStudent};

class GroupStudentEnrollAction
{
	public function execute($singleStudent, ?LessonGroup $lessonGroup, ?GroupStudent $existingGroupStudent, $CURRENT_USER, $MEMBER_UID, $isNewRecord)
	{
		# Due to proprietary secrecy, code that contained business rules were removed
	}
}