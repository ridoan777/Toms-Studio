<?php

use App\Http\Controllers\System\Role_Management\RoleController;
use Illuminate\Support\Facades\Route;

// ----------------- ANALYTICS -----------------
Route::prefix('admin')->middleware('auth')->group(function () {

    Route::get('/roles/index', [RoleController::class, 'index'])->name('backend_roles_index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('backend_roles_create');
    Route::get('/roles/show/{role}', [RoleController::class, 'show'])->name('backend_roles_show');
    
    Route::post('/roles/store', [RoleController::class, 'store'])->name('backend_roles_store');
    Route::put('/roles/update/{role}', [RoleController::class, 'update'])->name('backend_roles_update');
    
    Route::delete('/roles/{role}/delete', [RoleController::class, 'delete'])->name('backend_roles_delete');

});
// ----------------- ANALYTICS -----------------