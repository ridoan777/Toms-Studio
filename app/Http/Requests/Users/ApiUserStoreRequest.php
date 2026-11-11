<?php

namespace App\Http\Requests\Users;

use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiUserStoreRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$flag = $this->input('flag');
		return [
			'id' => 'nullable|integer|exists:users,id',
			'name' => ['required', 'string', 'min:4', 'max:255'],
			'user_role' => ['required', 'string', 'in:agent,tenant,workman'],
			'email' => [
				'required',
				'string',
				'lowercase',
				'email',
				'max:255',
				'email' => ['required','string','lowercase','email','max:255'],
			],
			'password' => 'required|string|confirmed|max:60|min:8',
			'agency' => ['nullable', 'string', 'max:255'],
			'skills' => ['nullable', 'array', 'max:50'],
			'skills.*' => ['string', 'max:100'],

		];
	}
}
