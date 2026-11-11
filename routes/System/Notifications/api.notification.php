<?php

use App\Http\Controllers\System\Notifications\ApiInAppNotificationController;
use App\Http\Controllers\System\Notifications\PushNotificationController;
use Illuminate\Support\Facades\Route;


// ----------------- IN-APP NOTIFICATIONS -----------------

Route::prefix('user')->middleware(['auth:sanctum'])->group(function () {

	Route::get('/notifications/in-app/index', [ApiInAppNotificationController::class, 'index']);

	Route::delete('/notifications/in-app/{notify}/delete', [ApiInAppNotificationController::class, 'delete']);
});
// ----------------- IN-APP NOTIFICATIONS -----------------

// ----------------- PUSH NOTIFICATIONS -----------------
Route::middleware(['auth:sanctum'])->post('/fcm/token', [PushNotificationController::class, 'saveFcmToken']);

// ----------------- PUSH NOTIFICATIONS -----------------
