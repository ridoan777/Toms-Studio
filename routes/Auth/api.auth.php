<?php

use App\Http\Controllers\Auth\Api\{ApiAuthController,ApiSocialAuthController};
use Illuminate\Support\Facades\Route;

// this route file is registered at bootstrap/app.php under api array


Route::prefix('auth')->group(function () {
   // Public routes
   Route::post('/register', [ApiAuthController::class, 'register'])->middleware('throttle:30,5');
   Route::post('/verify-email', [ApiAuthController::class, 'verifyEmail'])->middleware('throttle:30,5');
   Route::post('/resend-otp', [ApiAuthController::class, 'resendOTP'])->middleware('throttle:30,1');

   Route::post('/login', [ApiAuthController::class, 'login'])->middleware('throttle:30,5');

   Route::post('/forgot-password', [ApiAuthController::class, 'forgotPassword'])->middleware('throttle:30,5');
   Route::post('/verify-password-reset-otp', [ApiAuthController::class, 'verifyPasswordResetOTP'])->middleware('throttle:30,1');
   Route::post('/reset-password', [ApiAuthController::class, 'resetPassword'])->middleware('throttle:30,5');

   // Protected routes
   Route::post('/logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');
});

// ------------------ SOCIAL LOGINS::MOBILE ------------------
Route::prefix('auth')->group(function () {
   Route::post('/social/{provider}', [ApiSocialAuthController::class, 'mobileSocialLogin']);
});