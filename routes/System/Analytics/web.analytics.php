<?php

use App\Http\Controllers\System\Analytics\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('backend_analytics_index');
});

// ----------------- ANALYTICS -----------------
Route::prefix('admin/charts')->middleware('auth')->group(function () {

    Route::get('/users/roles', [AnalyticsController::class, 'personnels']);

    Route::get('/projects/phases', [AnalyticsController::class, 'projectPhasesChart']);

    Route::get('/projects/closing-date', [AnalyticsController::class, 'projectClosingLines']);

    Route::get('/deliveries/schedule-date', [AnalyticsController::class, 'deliveryScheduleLines']);

    Route::get('/inventory/tools/usage', [AnalyticsController::class, 'toolsUsageBarNLines']);

});
// ----------------- ANALYTICS -----------------