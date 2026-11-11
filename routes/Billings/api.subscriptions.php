<?php

use App\Http\Controllers\Billings\Checkouts\StripeSubscribeCheckoutController;
// use App\Http\Controllers\Billings\Tiers\ApiSubscriptionTierController;
use App\Http\Controllers\Billings\Webhooks\{RevenueCatWebhookController,StripeWebhookController};
use Illuminate\Support\Facades\{Auth, Route};

// ------------------------- GET TIERS/PACKAGES -------------------------
Route::prefix('users')->middleware(['auth:sanctum'])->group(function () {

   // Route::get('/billing/subscriptions/web/tiers/index', [ApiSubscriptionTierController::class, 'indexWeb']);
   // Route::get('/billing/subscriptions/mobile/tiers/index', [ApiSubscriptionTierController::class, 'indexMobile']);
});
// ------------------------- GET TIERS/PACKAGES -------------------------


// ------------------------- STRIPE + WEBHOOKS -------------------------
Route::prefix('stripe')->group(function () {

   Route::post('/tiers/purchase', [StripeSubscribeCheckoutController::class, 'checkout'])->name('stripe_checkout')->middleware(['auth:sanctum']);

   Route::get('https://checkout.stripe.com/c/pay/{stripe_checkout_session_id}');

   Route::get('/checkout/success', [StripeSubscribeCheckoutController::class, 'success'])->name('checkout_success');
   Route::get('/checkout/cancel', [StripeSubscribeCheckoutController::class, 'cancel'])->name('checkout_cancel');

   Route::post('/master-admin/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');
});
// ------------------------- STRIPE + WEBHOOKS -------------------------


// ------------------------- REVENUECAT + WEBHOOKS -------------------------
Route::prefix('revenuecat')->group(function () {

   Route::post('/sandbox/webhook', [RevenueCatWebhookController::class, 'handleWebhook']);

});
// ------------------------- REVENUECAT + WEBHOOKS -------------------------