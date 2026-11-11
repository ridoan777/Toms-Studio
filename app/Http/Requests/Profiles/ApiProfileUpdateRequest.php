<?php

namespace App\Http\Requests\Profiles;

use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiProfileUpdateRequest extends FormRequest
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
			'current_password' => ['nullable', 'string', 'min:8'],
			'phone' => ['nullable', 'string', 'min:1'],
			'gender' => ['nullable', 'string', 'in:male,female,prefer_not'],
			'about' => ['nullable', 'string', 'min:10, max:400'],
			'timezone' => ['nullable', 'string', 'max:255'],
			
			'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10485',
			'remove_avatar' => 'nullable|boolean',
		];
	}
}
