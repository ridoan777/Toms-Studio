<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mpdf\Tag\A;

// ----------------- FRONT-END (IN DASHBOARD) -----------------
Route::get('/', function () {
    return view('dashboard');
    // return view('welcome_13');
})->middleware(['auth', 'verified'])->name('dashboard');

// require __DIR__.'/Auth/web.auth.php';
