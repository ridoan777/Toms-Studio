<?php

namespace App\Http\Controllers\Billings\Webhooks;

use App\Domains\Billings\Enums\{TrxPhaseEnums, FallbackTrxPaymentStatusEnums, ItemPhaseEnums};
use App\Helpers\Errors\ExceptionHandling;
use App\Models\Billings\Payments\{PaymentTrx, PaymentItem, PaymentPayable};
use App\Models\Workflows\Lessons\{Lesson, LessonGroup};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Cache, Log, DB};
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Carbon\Carbon;
use Stripe\Exception\SignatureVerificationException;
use Throwable;
// this is the new one, v3
class StripeWebhookController extends CashierWebhookController
{
	public function handleWebhook(Request $request)
	{
		$this->showLog(['local', 'staging'], 'handleWebhook():', [$request->all()]);
		try {
			return parent::handleWebhook($request);
		} catch (SignatureVerificationException $e) {
			Log::warning('Stripe webhook signature verification failed', ['message' => $e->getMessage()]);
			return response()->json(['error' => 'Invalid signature'], 200);
		} catch (Throwable $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	// -------------- 1| CHECKOUT SESSION COMPLETED --------------

	protected function handleCheckoutSessionCompleted(array $payload)
	{
		# Due to proprietary secrecy, code that contained business rules were removed
	}
	// -------------- 2| CAHRGE UPDATED (PAYMENT SUCCESS) --------------

	protected function handleChargeUpdated(array $payload)
	{
		# Due to proprietary secrecy, code that contained business rules were removed
	}
	// -------------- 3| SESSION EXPIRED --------------

	protected function handleCheckoutSessionExpired(array $payload)
	{
		# Due to proprietary secrecy, code that contained business rules were removed
	}
	// -------------- 4| INTENT FAILED --------------

	protected function handlePaymentIntentPaymentFailed(array $payload)
	{
		# Due to proprietary secrecy, code that contained business rules were removed
	}
	// -------------- HELPER METHODS --------------

	protected function showLog($env = ['local'], $methodName, $logBody = [])
	{
		# Due to proprietary secrecy, code that contained business rules were removed
	}

	protected function fallbackPaymentItems(?PaymentTrx $paymentTrx, ?string $failureMessage): void
	{
		# Due to proprietary secrecy, code that contained business rules were removed
	}
}
