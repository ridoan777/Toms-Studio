<?php

namespace App\Http\Requests\Workflows;

use App\Enums\LessonTypeEnums;
use App\Domains\Lessons\Enums\LessonPhaseEnums;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LessonHistoryStoreRequest extends FormRequest
{
	public function rules(): array
	{
		$type = $this->input('type');
		return [
			'id' => 'nullable|integer|exists:lesson_histories,id',
			'type' => ['required', 'string', Rule::in(array_column(LessonTypeEnums::cases(), 'value'))],
			'lesson_id' => [Rule::requiredIf($type === LessonTypeEnums::LESSON->value), 'nullable', 'integer', 'exists:lessons,id'],
			'group_id' => [Rule::requiredIf($type === LessonTypeEnums::GROUP->value), 'nullable', 'integer', 'exists:lesson_groups,id'],
			'phase' => ['required', Rule::in(LessonPhaseEnums::values())],
			'original_date' => [Rule::requiredIf($this->phase === LessonPhaseEnums::SCHEDULED->value), 'nullable', 'date'],
			'final_date' => [Rule::requiredIf($this->phase === LessonPhaseEnums::SCHEDULED->value), 'nullable', 'date'],
			'final_start_time' => [Rule::requiredIf($this->phase === LessonPhaseEnums::SCHEDULED->value), 'nullable', 'date_format:H:i'],
			'final_end_time' => [Rule::requiredIf($this->phase === LessonPhaseEnums::SCHEDULED->value), 'nullable', 'date_format:H:i', 'after:final_start_time'],
		];
	}

	public function messages(): array
	{
		return [
			'final_end_time.after' => 'Final end time must be after start time.',
			'phase.in' => 'Invalid lesson phase selected.',
			'lesson_id.required_if' => 'The lesson field is required when type is lesson.',
			'group_id.required_if' => 'The group field is required when type is group.',
		];
	}
}