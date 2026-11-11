<?php

// use App\Events\MessageSent;

use App\Http\Controllers\Billings\Customers\WebCustomersController;
use Illuminate\Support\Facades\Route;

// ------------------------ CUSTOMERS ------------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {

   Route::get('/billings/customers/index', [WebCustomersController::class, 'index'])->name('backend_billings_customers_index');
});
// ------------------------ CSUTOMERS ------------------------


// ------------------------ SUBSCRIBERS ------------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {

   Route::get('/billings/customers/subscribers/index', [WebCustomersController::class, 'subscribers'])->name('backend_billings_customers_subscribers');

   Route::get('/billings/customers/subscribers/{subscriber}/show', [WebCustomersController::class, 'showSubscriber'])->name('backend_billings_subscriber_show');

   Route::post('/billings/customers/subscribers/{subscriber}/toggle', [WebCustomersController::class, 'toggleSubscriber'])->name('backend_billings_subscriber_toggle');

   Route::delete('/billings/customers/subscribers/{subscriber}/delete', [WebCustomersController::class, 'deleteSubscriber'])->name('backend_billings_subscriber_delete');

   Route::post('/billings/customers/subscribers/bulk-delete', [WebCustomersController::class, 'bulkDeleteSubscriber'])->name('backend_billings_subscriber_bulk_delete');

   Route::post('/billings/customers/subscribers/datatable', [WebCustomersController::class, 'subscribersDatatable'])->name('backend_billings_subscribers_datatable');
});
// ------------------------ SUBSCRIBERS ------------------------


// ------------------------ BUYERS ------------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {

   Route::get('/billings/customers/buyers/index', [WebCustomersController::class, 'buyers'])->name('backend_billings_customers_buyers');

   // Route::get('/billings/customers/tiers/{tier}/show', [WebCustomersController::class, 'show'])->name('backend_billings_customer_show');

   Route::post('/billings/customers/buyers/{buyers}/toggle', [WebCustomersController::class, 'toggleBuyer'])->name('backend_billings_buyer_toggle');

   // Route::delete('/billings/customers/tiers/{tier}/delete', [WebCustomersController::class, 'delete'])->name('backend_billings_customer_delete');

   // Route::post('/billings/customers/subscribers/datatable', [WebCustomersController::class, 'subscribersDatatable'])->name('backend_billings_subscribers_datatable');
});
// ------------------------ BUYERS ------------------------
