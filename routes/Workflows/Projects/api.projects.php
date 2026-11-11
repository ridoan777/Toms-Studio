<?php

use App\Http\Controllers\Workflows\Projects\ApiProjectController;
use Illuminate\Support\Facades\Route;


// ------------ PROJECTS ------------

Route::middleware(['auth:sanctum'])->group(function () {

   Route::get('/projects/all', [ApiProjectController::class, 'index']);
   Route::get('/projects/single-project/{singleProject}/show', [ApiProjectController::class, 'show']);

   Route::post('/projects/single-project/store', [ApiProjectController::class, 'store']);
});
// ------------ PROJECTS ------------