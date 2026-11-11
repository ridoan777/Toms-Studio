<?php

// use App\Events\MessageSent;

use App\Http\Controllers\Billings\Transactions\WebTransactionsController;
use Illuminate\Support\Facades\Route;

// ------------------------ TIERS ------------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {
   
   Route::get('/billings/transactions/tiers/index', [WebTransactionsController::class, 'index'])->name('backend_transactions_tiers_index');

   // Route::get('/billings/transactions/tiers/{tier}/show', [WebTransactionsController::class, 'show'])->name('backend_transactions_tier_show');

   // Route::post('/billings/transactions/tiers/store', [WebTransactionsController::class, 'store'])->name('backend_transactions_tier_store');

   // Route::delete('/billings/transactions/tiers/{tier}/delete', [WebTransactionsController::class, 'delete'])->name('backend_transactions_tier_delete');

   // Route::post('/billings/transactions/tiers/datatable', [WebTransactionsController::class, 'datatable'])->name('backend_transactions_tier_datatable');
});
// ------------------------ TIERS ------------------------
