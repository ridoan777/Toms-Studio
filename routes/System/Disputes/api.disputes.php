<?php

use App\Http\Controllers\System\Analytics\AnalyticsController;
use Illuminate\Support\Facades\Route;

// ----------------- ANALYTICS -----------------
Route::prefix('donuts')->middleware(['auth:web'])->group(function () {

   // Route::get('/users/roles', [AnalyticsController::class, 'userDonutChart']);

});
// ----------------- ANALYTICS -----------------

