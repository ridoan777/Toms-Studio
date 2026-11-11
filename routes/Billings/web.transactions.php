<?php

// use App\Events\MessageSent;

use App\Http\Controllers\Billings\Transactions\WebTransactionsController;
use Illuminate\Support\Facades\Route;

// ------------------------ TRANSACTIONS ------------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {
   
   Route::get('/billings/transactions/index', [WebTransactionsController::class, 'index'])->name('backend_transactions_index');

   Route::get('/billings/transactions/{transaction}/show', [WebTransactionsController::class, 'show'])->name('backend_transactions_show');

   Route::post('/billings/transactions/{transaction}/toggle', [WebTransactionsController::class, 'toggle'])->name('backend_transactions_toggle');

   Route::delete('/billings/transactions/{transaction}/delete', [WebTransactionsController::class, 'delete'])->name('backend_transactions_delete');

   Route::post('/billings/transactions/bulk-delete', [WebTransactionsController::class, 'bulkDelete'])->name('backend_transactions_bulk_delete');

   Route::post('/billings/transactions/datatable', [WebTransactionsController::class, 'datatable'])->name('backend_transactions_datatable');
});
// ------------------------ TRANSACTIONS ------------------------
