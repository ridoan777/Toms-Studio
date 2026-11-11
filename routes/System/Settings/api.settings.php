<?php

use App\Http\Controllers\System\Settings\ApiProfileController;
use App\Http\Controllers\System\Settings\ApiSettingsController;
use Illuminate\Support\Facades\Route;

// this route file is registered at bootstrap/app.php under api array


// ----------------- API SETTINGS [PROFILE] -----------------profile/email-update/resent-otp
Route::prefix('profile')->middleware(['auth:sanctum'])->group(function () {

   Route::get('/user/who-i-am', [ApiProfileController::class, 'index']);

   Route::post('/update', [ApiProfileController::class, 'update']);

   Route::post('/delete', [ApiProfileController::class, 'delete']);

   Route::post('/email-update/resent-otp', [ApiProfileController::class, 'resendUpdateOTP']);

   Route::post('/confirm-new-email', [ApiProfileController::class, 'confirmNewEmail']);

});
// ----------------- SITE SETUP -----------------


// ----------------- SITE SETUP -----------------

Route::prefix('settings')->middleware(['auth:sanctum'])->group(function () {

   Route::get('/static-pages', [ApiSettingsController::class, 'indexStaticPages']);

   Route::get('/site-setup', [ApiSettingsController::class, 'basicSiteSetup']);

});
// ----------------- IN-APP NOTIFICATIONS -----------------


// ----------------- SETTINGS PREFERENCE BY USERS -----------------

Route::prefix('user')->middleware(['auth:sanctum'])->group(function () {

   Route::post('/settings/preferences/store', [ApiSettingsController::class, 'preferenceStore']);

});
// ----------------- SETTINGS PREFERENCE BY USERS -----------------

