<?php

use App\Http\Controllers\Workflows\Students\ApiStudentsController;
use Illuminate\Support\Facades\Route;


// ------------ TECHER VIEWING STUDNETS ------------

Route::middleware(['auth:sanctum'])->group(function () {

   Route::get('/teacher/my-students/index', [ApiStudentsController::class, 'myStudents']);
   Route::get('/teacher/my-students/{student}/show', [ApiStudentsController::class, 'myStudentShow']);

});
// ------------ TECHER VIEWING STUDNETS ------------