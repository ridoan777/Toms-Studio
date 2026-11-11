<?php

use App\Http\Controllers\Auth\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Auth\Admin\ConfirmablePasswordController;
use App\Http\Controllers\Auth\Admin\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\Admin\EmailVerificationPromptController;
use App\Http\Controllers\Auth\Admin\NewPasswordController;
use App\Http\Controllers\Auth\Admin\PasswordController;
use App\Http\Controllers\Auth\Admin\PasswordResetLinkController;
use App\Http\Controllers\Auth\Admin\RegisteredUserController;
use App\Http\Controllers\Auth\Admin\VerifyEmailController;
use App\Http\Controllers\Auth\Admin\WebSocialAuthController;
use App\Http\Controllers\Auth\Api\ApiAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');

    Route::post('register', [RegisteredUserController::class, 'store'])->middleware('throttle:20,5');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:20,5');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request')->middleware('throttle:20,5');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email')->middleware('throttle:20,5');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store')->middleware('throttle:20,5');
});

Route::middleware('auth:web')->group(function () {
    // location = App\Http\Controllers\Auth\Admin\EmailVerificationPromptController.php

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice')->middleware('throttle:30,5');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->name('verification.verify')->middleware(['signed']);

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm')->middleware('throttle:30,5');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])->middleware('throttle:5,1');

    Route::put('password', [PasswordController::class, 'update'])->name('password.update')->middleware('throttle:5,1');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
    profile update was moved to routes\Admin\settings.php

*/
// ------------------ SOCIAL LOGINS::WEB ------------------
Route::prefix('auth')->group(function () {

   Route::get('/{provider}/redirect', [WebSocialAuthController::class, 'socialRedirect'])->name('web_auth_social_login_redirect');  // takes to the login page
   Route::get('/{provider}/callback', [WebSocialAuthController::class, 'socialCallback'])->name('web_auth_social_login_callback');  // sends data to our app server

});