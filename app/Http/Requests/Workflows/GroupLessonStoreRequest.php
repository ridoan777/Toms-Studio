<?php

namespace App\Http\Requests\Workflows;

use App\Enums\WeekdaysEnums;
use App\Domains\Lessons\Enums\{LessonDifficultyEnums, LessonPhaseEnums};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupLessonStoreRequest extends FormRequest
{
	public function rules(): array
	{
		return [
			'id' => 'nullable|integer|exists:lesson_groups,id',

			'lesson_uid' => 'nullable|string|max:50|unique:lessons,lesson_uid,' . $this->id,

			'teacher_id' => 'required|integer|exists:users,id',

			'students' => 'nullable|array',
			'students.*.id' => 'required|integer|exists:users,id',
			'students.*.fees' => 'required|numeric|min:0|max:999999.99',

			'subject_id' => 'required|integer|exists:instruments,id',

			'title' => 'required|string|max:255',

			'difficulty' => ['nullable', Rule::in(LessonDifficultyEnums::values())],

			'schedule_day' => 'nullable|array',
			'schedule_day.*' => [Rule::enum(WeekdaysEnums::class)],
			'starting_date' => 'required|date',

			'start_time' => 'nullable|date_format:H:i',
			'end_time' => 'nullable|date_format:H:i|after:start_time',

			'remarks' => 'nullable|string|max:1000',

			'last_class' => 'nullable|date',

			'status' => 'nullable|boolean',
		];
	}

	public function messages(): array
	{
		return [
			'teacher_id.exists' => 'Selected teacher is invalid.',
			'end_time.after' => 'End time must be after start time.',
			'phase.in' => 'Invalid lesson phase selected.',
			'difficulty.in' => 'Invalid difficulty level.',
			'students.*.fees.required' => 'The fee is required for each student.',
		];
	}
}
