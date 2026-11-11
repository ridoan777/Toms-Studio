<?php

namespace App\Http\Requests\System;

use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppPreferencesRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'status' => 'nullable|boolean|in:0,1',

			'in_app_notification' => 'nullable|boolean|in:0,1',
			'email_notification' => 'nullable|boolean|in:0,1',
			'push_notification' => 'nullable|boolean|in:0,1',
			'activity_log' => 'nullable|boolean|in:0,1',
		];
	}
}
