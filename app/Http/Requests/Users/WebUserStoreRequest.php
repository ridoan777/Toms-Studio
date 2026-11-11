<?php

namespace App\Http\Requests\Users;

use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebUserStoreRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'id' => 'nullable|integer|exists:users,id',
			'name' => 'required|string|max:255',
			'user_role' => 'required|string',	// keep role for RBAC matching. don't change to user_role

			'email' => 'required|email|max:255|unique:users,email,' . $this->id,
			'username' => 'nullable|string|min:4|max:20|unique:users,username,' . $this->id,
			'phone' => 'nullable|string|max:20',

			'guardian' => 'nullable|string|max:255',
			'position' => ['nullable','integer','exists:instruments,id'],
			'address' => 'nullable|string|max:255',

			'password' => $this->id
				? 'nullable|string|min:6|confirmed'
				: 'required|string|min:6|confirmed',

			'avatar' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
			'remove_image' => 'nullable|boolean',

			'mail_user' => 'nullable|boolean',
			'status' => 'nullable|boolean',
		];
	}

	public function messages(): array
	{
		return [
			'email.unique' => 'Another user already uses this email.',
			'password.confirmed' => 'Passwords do not match.',
		];
	}
}
