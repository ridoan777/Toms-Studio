<?php

namespace App\Http\Requests\Billings;

use App\Domains\Billings\Enums\ItemPhaseEnums;
use App\Models\Billings\Payments\PaymentItem;
use App\Models\Billings\Payments\PaymentPayable;
use App\Models\Workflows\Lessons\GroupStudent;
use App\Models\Workflows\Lessons\Lesson;
use App\Models\Workflows\Lessons\LessonGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CheckoutValidationRequest extends FormRequest
{
	public function authorize(): bool
	{
		return auth()->check();
	}

	public function rules(): array
	{
		return [
			'payment' => 'required|array',
			'payment.*.payable_uid' => 'required|string|exists:payment_payables,payable_uid',
		];
	}

	public function after(): array
	{
		return [
			function () {
				foreach ($this->validated()['payment'] as $index => $item) {
					$payable = PaymentPayable::payableUid($item['payable_uid'])->with('payable')->first();
					$trxItem = PaymentItem::payableUid($item['payable_uid'])->first();

					if (!$payable->payable) {
						throw ValidationException::withMessages(["payment.$index.payable_uid" => 'The linked lesson or group no longer exists.']);
					}
					if ($trxItem?->phase === ItemPhaseEnums::SYNCED->value || $payable->paid_at) {
						throw ValidationException::withMessages(["payment.$index.payable_uid" => 'This payment item is already paid. Select another one.']);
					}
					if($payable->cancel_resolve === 'accept' || (int)$payable->is_cancelled === 1){
						throw ValidationException::withMessages(["payment.$index.payable_uid" => 'This payment item was requested for cancellation & accepted. Select another one.']);
					}

					if ($payable->status !== 1) {
						throw ValidationException::withMessages(["payment.$index.payable_uid" => 'This payable is inactive. Contact support.']);
					}

					$studentExists = match ($payable->payable_type) {
						Lesson::class => (int)$payable->payable->student_id === (int)$payable->student_id,
						LessonGroup::class => GroupStudent::groupId($payable->payable_id)->where('student_id', $payable->student_id)->exists(),
						default => false,
					};

					if (!$studentExists) {
						throw ValidationException::withMessages(["payment.$index.payable_uid" => 'The student on this payable is not assigned to the linked lesson or group.']);
					}
				}
			}
		];
	}
}
