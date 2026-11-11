<?php

use App\Http\Controllers\Workflows\Homepages\{ApiTeacherHomepageController,ApiStudentHomepageController};
use Illuminate\Support\Facades\Route;


// ------------ HOMEPAGE ------------

Route::middleware(['auth:sanctum'])->group(function () {

   Route::get('/teacher/homepage/index', [ApiTeacherHomepageController::class, 'index']);
   Route::get('/student/homepage/index', [ApiStudentHomepageController::class, 'index']);

});
// ------------ HOMEPAGE ------------