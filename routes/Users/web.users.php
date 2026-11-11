<?php

use App\Http\Controllers\Users\WebAdminController;
use App\Http\Controllers\Users\WebUserController;
use App\Http\Controllers\Users\WebUserDatatableController;
// use App\Http\Controllers\Users\WebUserController;
use Illuminate\Support\Facades\Route;


// ------------ SIDEBAR::USERS ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {

   Route::get('/components', [WebUserController::class, 'components'])->name('backend.components');
   
   Route::get('/all-users', [WebUserController::class, 'index'])->name('backend_all_user');

   Route::get('/single-user/{user}/show', [WebUserController::class, 'show'])->name('backend_single_user');

   Route::delete('/user/{user}/delete', [WebUserController::class, 'delete'])->name('backend_user_delete');

   Route::post('/user/{users}/bulk-delete', [WebUserController::class, 'bulkDelete'])->name('backend_user_bulk_delete');

   Route::get('/user/{user}/manual-verify', [WebUserController::class, 'manualVerifyUserToggle'])->name('backend_user_manual_verify_store');

   Route::post('/all-users/table', [WebUserDatatableController::class, 'all_users_dataTable'])->name('backend_dataTable_all_users');
});
// ------------ SIDEBAR::USERS ------------


// ------------ SIDEBAR::CREATE-USERS ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {

   Route::get('/create-users', [WebUserController::class, 'create'])->name('backend_create_users');

   Route::post('/create-users/new/store', [WebUserController::class, 'store'])->name('backend_create_users_new_store');
});
// ------------ SIDEBAR::CREATE-USERS ------------


// ------------ VERIFIACTION ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {

   Route::post('/update/verify-email', [WebAdminController::class, 'verifyEmail'])->name('backend_user_update_verify_email');

   Route::post('/update/verify-email/resend-otp', [WebAdminController::class, 'resendOTP'])->name('backend_verify_email_resent_otp');

   Route::delete('/update/verify-email/{userVerification}/delete-attempt', [WebAdminController::class, 'deleteAttempt'])->name('backend_verify_email_delete');
});
// ------------ VERIFIACTION ------------


// ------------ SIDEBAR::BLACKLIST ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {

   Route::get('/blacklisted-users', [WebUserController::class, 'indexBlackListedUsers'])->name('backend_user_blacklist');

   Route::post('/user/toggle/{user}/blacklist', [WebUserController::class, 'toggle'])->name('backend_user_toggle');

   Route::post('/blacklisted-users/table', [WebUserDatatableController::class, 'blacklisted_users_dataTable'])->name('backend_dataTable_blacklisted_users');
});
// ------------ SIDEBAR::BLACKLIST ------------


// ------------ SIDEBAR::PENDING ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {

   Route::get('/pending-users', [WebUserController::class, 'indexPendingUsers'])->name('backend_user_pending');

   Route::post('/pending-users/table', [WebUserDatatableController::class, 'pending_users_dataTable'])->name('backend_dataTable_pending_users');
});
// ------------ SIDEBAR::PENDING ------------


// ------------ SIDEBAR::ADMIN ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {

   Route::get('/all-admins/index', [WebUserController::class, 'indexAdminUsers'])->name('backend_user_admins');

   Route::post('/all-admins/table', [WebUserDatatableController::class, 'admin_users_dataTable'])->name('backend_dataTable_admin_users');
});
// ------------ SIDEBAR::ADMIN ------------


// ------------ SIDEBAR::ROLES ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {

   Route::get('/users/{customRole}/index', [WebUserController::class, 'indexRoledUsers'])->name('backend_user_role_based');

   Route::post('/users/{customRole}/table', [WebUserDatatableController::class, 'roled_users_dataTable'])->name('backend_dataTable_teacher_users');
});
// ------------ SIDEBAR::ROLES ------------
