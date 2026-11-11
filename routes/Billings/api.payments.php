<?php

use App\Http\Controllers\Billings\Payables\ApiPayableController;
use App\Http\Controllers\Billings\Checkouts\StripeOneTimePayCheckoutController;
use App\Http\Controllers\Billings\Webhooks\StripeWebhookController;
use Illuminate\Support\Facades\{Auth, Route};


// ------------------------- PAYABLES -------------------------
Route::prefix('student')->middleware(['auth:sanctum'])->group(function () {

   Route::get('/payables/my-records/view', [ApiPayableController::class, 'index']);
   Route::get('/payables/cancelled-records/view', [ApiPayableController::class, 'cancelled']);


   Route::post('/payables/cancellation-request', [ApiPayableController::class, 'cancelRequest']);
});
// ------------------------- PAYABLES -------------------------


// ------------------------- STRIPE + CHECKOUTS -------------------------
Route::middleware(['auth:sanctum'])->prefix('stripe')->group(function () {

   Route::post('/payments/checkout', [StripeOneTimePayCheckoutController::class, 'checkout']);
});

Route::prefix('stripe')->group(function () {

   Route::get('/payments/success', [StripeOneTimePayCheckoutController::class, 'success'])->name('api_payment_success');
   Route::get('/payments/cancel', [StripeOneTimePayCheckoutController::class, 'cancel'])->name('api_payment_cancel');
});
// ------------------------- STRIPE + CHECKOUTS -------------------------


// ------------------------- STRIPE + WEBHOOKS -------------------------
Route::prefix('stripe')->group(function () {

   Route::post('/sandbox/payments/webhook', [StripeWebhookController::class, 'handleWebhook']);
   Route::post('/live/payments/webhook', [StripeWebhookController::class, 'handleWebhook']);
});
// ------------------------- STRIPE + WEBHOOKS -------------------------

/*
   LOCAL:
   stripe listen --forward-to localhost:8000/api/stripe/sandbox/payments/webhook

   Card declined (generic): 4000 0000 0000 0002 
   Insufficient funds: 4000 0000 0000 9995 
   Authentication required then fails: 4000 0025 0000 3155
*/
/*
   PRODUCTION:
      url -X POST https://dashboard.tomsperformancestudio.cloud/api/stripe/sandbox/payments/webhook

      expecting log:
      [2026-06-17 09:53:29] production.INFO: Custom WebhookController: handleWebhook(): [[]] 
*/
