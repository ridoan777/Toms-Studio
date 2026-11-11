<?php

namespace App\Http\Requests\Workflows;

use App\Enums\{LessonCategoryEnums,WeekdaysEnums};
use App\Domains\Lessons\Enums\{LessonDifficultyEnums,LessonPhaseEnums};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LessonStoreRequest extends FormRequest
{
	public function rules(): array
	{
		// dd(request()->all());
		return [
			'id' => 'nullable|integer|exists:lessons,id',

			'lesson_uid' => 'nullable|string|max:50|unique:lessons,lesson_uid,' . $this->id,

			'teacher_id' => 'required|integer|exists:users,id',
			'student_id' => 'required|integer|exists:users,id',
			'subject_id' => 'required|integer|exists:instruments,id',

			'title' => 'required|string|max:255',

			// 'phase' => 'required|string|in:upcoming,in_progress,scheduled,completed,no_show,cancelled',
			// 'phase' => ['required', Rule::in(LessonPhaseEnums::values())],
			'lesson_category' => ['required', Rule::in(array_column(LessonCategoryEnums::cases(), 'value'))],
			'difficulty' => ['nullable', Rule::in(LessonDifficultyEnums::values())],

			'fees' => 'nullable|numeric|min:0|max:999999.99',

			'schedule_day' => 'required|array|min:1|max:7',
			'schedule_day.*' => [Rule::enum(WeekdaysEnums::class)],
			'starting_date' => 'required|date',

			'start_time' => 'required|',
			'end_time' => 'required|after:start_time',

			'remarks' => 'nullable|string|max:10000',

			'status' => 'nullable|boolean',
		];
	}

	public function messages(): array
	{
		return [
			'teacher_id.exists' => 'Selected teacher is invalid.',
			'student_id.exists' => 'Selected student is invalid.',
			'end_time.after' => 'End time must be after start time.',
			'phase.in' => 'Invalid lesson phase selected.',
			'difficulty.in' => 'Invalid difficulty level.',
		];
	}
}