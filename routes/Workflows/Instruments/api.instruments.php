<?php

use App\Http\Controllers\Workflows\Positions\ApiInstrumentController;
use Illuminate\Support\Facades\Route;


// ------------ POSITIONS ------------

Route::middleware(['auth:sanctum'])->group(function () {

   Route::get('/instruments/index', [ApiInstrumentController::class, 'index']);
});
// ------------ POSITIONS ------------