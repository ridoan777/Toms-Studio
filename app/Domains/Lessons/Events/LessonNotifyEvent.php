<?php

namespace App\Domains\Lessons\Events;

use App\Enums\{CRUDActionEnums,NotifyChannelEnums};
use App\Models\Users\User;
use App\Models\Workflows\Lessons\Lesson;
use App\Models\Workflows\Lessons\LessonGroup;
use Illuminate\Broadcasting\{Channel, InteractsWithSockets, PrivateChannel};
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// check app\Providers\BroadcastServiceProvider.php
class LessonNotifyEvent
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

		public function __construct(
		public Lesson|LessonGroup $lesson,
		public ?string $objectName = null,
		public User $actor,
		public CRUDActionEnums $actionFlag,
		public array $channels = [NotifyChannelEnums::PUSH, NotifyChannelEnums::IN_APP],
		public array $force = [], // [NotifyChannelEnums::PUSH, NotifyChannelEnums::IN_APP]
		public ?array $targetUsers = []
	) {
	}
}
/*
// -------------------- NOTIFICATION EVENTS --------------------
$actionFlag = empty($validated['id']) ? CRUDActionEnums::CREATE : CRUDActionEnums::UPDATE;
$targetUsers = [(int)$validated['teacher_id'], (int)$validated['student_id']];

or, 

$studentIds = $lessonGroup->groupRelationWith_Students->pluck('student_id')->filter()->map(fn($id) => (int)$id)->toArray();
$targetUsers = array_values(array_unique(array_filter(array_merge([(int)$teacher], $studentIds))));

call,

event(new LessonNotifyEvent($lesson, null, $USER, $actionFlag, [NotifyChannelEnums::PUSH, NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], $targetUsers));
// -------------------- NOTIFICATION EVENTS --------------------
*/