<?php

namespace App\Http\Requests\Profiles;

use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebProfileUpdateRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'name' => ['nullable', 'string', 'min:4', 'max:255'],
			'email' => [
				'nullable',
				'string',
				'lowercase',
				'email',
				'max:255',
				Rule::unique(User::class)->ignore($this->user()->id),
			],
			// 'role' => 'required|string',
			'current_password' => ['nullable', 'string', 'min:8'],
			'phone' => ['nullable', 'string', 'max:1'],
			'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10485',
			'remove_image' => 'nullable|boolean',
		];
	}
}
