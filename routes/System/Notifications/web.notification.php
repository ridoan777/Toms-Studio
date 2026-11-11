<?php

use App\Http\Controllers\System\Notifications\WebNotificationController;
use Illuminate\Support\Facades\Route;


// ----------------- IN-APP NOTIFICATIONS -----------------

Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {

    Route::get('/system/logs/notifications/in-app/index', [WebNotificationController::class, 'index'])->name('backend_system_in_app_notify_index');

    Route::post('/system/logs/notifications/in-app/{notify}/toggle', [WebNotificationController::class, 'toggle'])->name('backend_system_in_app_notify_toggle');

    Route::delete('/system/logs/notifications/in-app/{notify}/delete', [WebNotificationController::class, 'delete'])->name('backend_system_in_app_notify_delete');

    Route::post('/system/logs/notifications/in-app/bulk-delete', [WebNotificationController::class, 'bulkDelete'])->name('backend_system_in_app_notify_bulk_delete');

    Route::post('/system/logs/notifications/in-app/datatable', [WebNotificationController::class, 'inAppNotifyDatatable'])->name('backend_system_in_app_notify_datatable');
});
// ----------------- IN-APP NOTIFICATIONS -----------------
