<?php

use App\Http\Controllers\Users\ApiUserController;
use Illuminate\Support\Facades\Route;


// ----------------- USER [PUBLIC::PERMISSION::User_prefiexed] -----------------
Route::prefix('public')->middleware(['auth:sanctum'])->group(function () {

   Route::get('/users/all/index', [ApiUserController::class, 'index']);
   Route::get('/users/{roleKey}/index', [ApiUserController::class, 'indexByRolekey']);

   Route::get('/user/{user}/show', [ApiUserController::class, 'show']);
});
// ----------------- USER [PUBLIC] -----------------