<?php

namespace App\Http\Requests\Workflows;

use Illuminate\Foundation\Http\FormRequest;

class NoteStoreRequest extends FormRequest
{
	public function rules(): array
	{
		return [
			'id' => 'nullable|integer|exists:lesson_notes,id',

			// use ApiLessonController::myStudents() to load students
			'lesson_id' => 'required_without:group_id|nullable|integer|exists:lessons,id',
			'group_id' => 'required_without:lesson_id|nullable|integer|exists:lesson_groups,id',

			'student_id' => 'required|integer|exists:users,id',
			// 'student_id' => 'required|array|min:1',
			// 'student_id.*' => 'required|integer|exists:users,id',

			'title' => 'required|string|max:255',
			'note' => 'nullable|string|max:1000',

			'video' => 'nullable|array|max:10',
			'video.*.id' => 'nullable|integer|exists:lesson_note_galleries,id',
			'video.*.file' => 'nullable|file|mimes:mp4,mov,mkv,ogg,qt|max:512000',
			'video.*.remove_file' => 'required|boolean',

			'audio' => 'nullable|array|max:10',
			'audio.*.id' => 'nullable|integer|exists:lesson_note_galleries,id',
			'audio.*.file' => 'nullable|file|mimes:mp3,wav,aac,m4a,ogg|max:51200',
			'audio.*.remove_file' => 'required|boolean',

			'doc' => 'nullable|array|max:10',
			'doc.*.id' => 'nullable|integer|exists:lesson_note_galleries,id',
			'doc.*.file' => 'nullable|file|mimes:pdf,doc,docx,txt,csv,xls,xlsx|max:20480',
			'doc.*.remove_file' => 'required|boolean',

			'status' => 'nullable|boolean|in:0,1',
		];
	}

	public function messages(): array
	{
		return [
			// 'difficulty.in' => 'Invalid difficulty level.',
		];
	}
}