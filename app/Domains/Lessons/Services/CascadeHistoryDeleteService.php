<?php

namespace App\Domains\Lessons\Services;

use App\Models\Billings\Payments\PaymentPayable;
use App\Models\Workflows\Lessons\LessonHistory;
use App\Models\Workflows\Lessons\Lesson;
use App\Models\Workflows\Lessons\LessonGroup;

class CascadeHistoryDeleteService
{
   public function handle($parentObject, string $type): void
   {
      if ($type === 'lesson') {
         LessonHistory::lessonId($parentObject->id)->delete();

         PaymentPayable::query()
            ->where('payable_type', Lesson::class)
            ->where('payable_id', $parentObject->id)
            ->whereNull('paid_at')
            ->delete();
      } else {
         LessonHistory::groupId($parentObject->id)->delete();

         PaymentPayable::query()
            ->where('payable_type', LessonGroup::class)
            ->where('payable_id', $parentObject->id)
            ->whereNull('paid_at')
            ->delete();
      }
   }
}
