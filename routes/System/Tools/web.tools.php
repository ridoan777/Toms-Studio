<?php

use Illuminate\Support\Facades\Route;
use RahulHaque\Filepond\Http\Controllers\FilepondController;

Route::group(['middleware' => config('filepond.middleware', ['web', 'auth'])], function () {
   Route::post(config('filepond.server.url', '/filepond'), [config('filepond.controller', FilepondController::class), 'process'])->name('filepond_process');
   Route::patch(config('filepond.server.url', '/filepond'), [config('filepond.controller', FilepondController::class), 'patch'])->name('filepond_patch');
   Route::get(config('filepond.server.url', '/filepond'), [config('filepond.controller', FilepondController::class), 'head'])->name('filepond_head');
   Route::delete(config('filepond.server.url', '/filepond'), [config('filepond.controller', FilepondController::class), 'revert'])->name('filepond_revert');
});