<?php

namespace App\Http\Requests\Communications;

use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChatMessageRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'message' => 'required|string',
			'receiver_id' => 'required|integer|exists:users,id',
			'file' => 'nullable|array',
			'file.*' => [
				'file',
				'max:102400',
				'mimetypes:image/jpeg,image/png,image/gif,image/webp,audio/mpeg,audio/mp4,audio/x-m4a,audio/wav,audio/ogg,video/mp4,video/quicktime,video/x-msvideo,video/webm,video/x-matroska,application/pdf,text/plain,text/csv,application/csv,application/json,text/json,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/rtf,text/rtf,application/x-rtf',
				function ($attribute, $value, $fail) {
					$allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp3', 'm4a', 'wav', 'ogg', 'mp4', 'mov', 'avi', 'mkv', 'webm', 'pdf', 'txt', 'csv', 'json', 'doc', 'docx', 'xls', 'xlsx', 'rtf'];
					if (!in_array(strtolower($value->getClientOriginalExtension()), $allowedExts)) {
						$fail('Only these file types are allowed: jpeg, png, gif, webp images; mp3, m4a, wav audio; mp4, mov, avi, webm videos; pdf, txt, csv, json, doc, docx, xls, xlsx, rtf documents.');
					}
				},
			],
		];
	}
}