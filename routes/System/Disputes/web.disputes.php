<?php

use App\Http\Controllers\System\Dispute_Management\WebDisputeManagementController;
use Illuminate\Support\Facades\Route;

// --------------------- DISPUTE MANAGEMENT ---------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {

    Route::get('/displute-management/index', [WebDisputeManagementController::class, 'index'])->name('backend_disputes_report_index');

    Route::post('/displute-management/reports/{report}/resolution', [WebDisputeManagementController::class, 'resolution'])->name('backend_disputes_report_handle');


    Route::get('displute-management/reports/{report}/show', [WebDisputeManagementController::class, 'show'])->name('backend_displute_report_show');

    Route::post('displute-management/report/{report}/toggle', [WebDisputeManagementController::class, 'toggle'])->name('backend_displute_report_toggle');

    Route::delete('displute-management/report/{report}/delete', [WebDisputeManagementController::class, 'delete'])->name('backend_displute_report_delete');

    Route::post('/displute-management/reports/datatable', [WebDisputeManagementController::class, 'reportsDatatable'])->name('backend_disputes_reports_datatable');
});
// --------------------- DISPUTE MANAGEMENT ---------------------

// --------------------- USER ACCESS ---------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {

    Route::get('/user-access/index', [WebDisputeManagementController::class, 'indexUserAccess'])->name('backend_user_access_index');
});
// --------------------- USER ACCESS ---------------------