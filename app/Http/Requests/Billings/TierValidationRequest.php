<?php

namespace App\Http\Requests\Billings;

use Illuminate\Foundation\Http\FormRequest;

class TierValidationRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return auth()->check();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'id' => 'nullable|integer|exists:subscription_tiers,id',
			'service_id' => 'nullable|integer|exists:projects,id',
			'platform' => 'required|string|in:mobile,web',
			'status' => 'nullable|boolean|in:0,1',

			'name' => 'required|string|max:255',
			'slogan' => 'nullable|string|max:255',

			'starting_price' => 'nullable|numeric|min:0',
			'final_price' => 'required|numeric|min:0',

			'price_label' => 'nullable|string|max:255',
			'duration' => 'nullable|numeric|min:0',

			'benefits' => 'nullable|string|max:2000',
			'description' => 'nullable|string|max:2000',

			'rc_entitlement_id' => 'nullable|string|max:255',
			'apple_product_id' => 'nullable|string|max:255',
			'google_subscription_id' => 'nullable|string|max:255',
			'google_base_plan_id' => 'nullable|string|max:255',

			'stripe_product_id' => 'nullable|string|max:255',
			'stripe_price_id' => 'nullable|string|max:255',

			'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:10485',
			'remove_image' => 'nullable|boolean',
		];
	}

	public function messages(): array
	{
		return [
			'project_manager_id.required' => "The project manager field is required.",
			'description.max' =>
				"The description field must not be greater than 2000 characters. Use 'Ctrl+Shift+V' for copy-pasting or choose plain text.",
			'target_date.after_or_equal' =>
				"The target completion date must be after or equal to the start date.",
		];
	}
}
