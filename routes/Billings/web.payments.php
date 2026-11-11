<?php

use App\Http\Controllers\Billings\Payables\WebPayableController;
use App\Http\Controllers\Billings\Payables\ManualPayablePaymentController;
use Illuminate\Support\Facades\Route;

// ------------------------ PAYABLES ------------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {

   Route::get('/billings/payables/index', [WebPayableController::class, 'index'])->name('backend_billings_payables_index');

   Route::get('/billings/payables/{payable}/show', [WebPayableController::class, 'show'])->name('backend_billings_payable_show');

   Route::post('/billings/payables/{payable}/manual-payment', [ManualPayablePaymentController::class, 'store'])->name('backend_billings_payable_manual_payment');

   Route::post('/billings/payables/{payable}/toggle', [WebPayableController::class, 'toggle'])->name('backend_billings_payable_toggle');

   Route::get('/billings/payables/{payable}/{flag}/resolve/cancellation', [WebPayableController::class, 'cancel'])->name('backend_billings_payable_resolve_cancellation');

   Route::delete('/billings/payables/{payable}/delete', [WebPayableController::class, 'delete'])->name('backend_billings_payable_delete');

   Route::post('/billings/payables/bulk-delete', [WebPayableController::class, 'bulkDelete'])->name('backend_billings_payable_bulk_delete');

   Route::post('/billings/payables/datatable', [WebPayableController::class, 'datatablePayable'])->name('backend_billings_payables_datatable');
});
// ------------------------ PAYABLES ------------------------

// ------------------------ CANCEL REQUESTS ------------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {

   Route::get('/billings/payables/cancel-requests/index', [WebPayableController::class, 'indexCancelReqs'])->name('backend_billings_payable_cancel_req_index');

   Route::post('/billings/payables/cancel-request/datatable', [WebPayableController::class, 'datatableCancelRequests'])->name('backend_billings_payables_cancel_req_datatable');
});
// ------------------------ CANCEL REQUESTS ------------------------

// ------------------------ CANCELLED DUES ------------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {

   Route::get('/billings/payables/cancellation/index', [WebPayableController::class, 'indexCancellation'])->name('backend_billings_payable_cancellation_index');

   Route::post('/billings/payables/cancellation/datatable', [WebPayableController::class, 'datatableCancellation'])->name('backend_billings_payables_cancellation_datatable');
});
// ------------------------ CANCELLED DUES ------------------------

// ------------------------ PAID ------------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {

   Route::get('/billings/payables/paid/index', [WebPayableController::class, 'indexPaid'])->name('backend_billings_payable_paid_index');

   Route::post('/billings/payables/paid/datatable', [WebPayableController::class, 'datatablePaid'])->name('backend_billings_payables_paid_datatable');
});
// ------------------------ PAID ------------------------

// ------------------------ UNPAID ------------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {

   Route::get('/billings/payables/unpaid/index', [WebPayableController::class, 'indexUnpaid'])->name('backend_billings_payable_unpaid_index');

   Route::post('/billings/payables/unpaid/datatable', [WebPayableController::class, 'datatableUnpaid'])->name('backend_billings_payables_unpaid_datatable');
});
// ------------------------ UNPAID ------------------------
