<?php

use App\Http\Controllers\Workflows\Lessons\ApiLessonController;
use App\Http\Controllers\Workflows\Lessons\ApiLessonHistoryController;
use Illuminate\Support\Facades\Route;


// ------------ LESSONS ------------

Route::middleware(['auth:sanctum'])->group(function () {

   Route::get('/lessons/index', [ApiLessonController::class, 'index']);
   
   Route::get('/lessons/my-lessons/index', [ApiLessonController::class, 'myLessons']);
   Route::get('/lessons/my-make-ups/view', [ApiLessonController::class, 'myMakeUps']);
   
   Route::get('/lessons/{lesson}/show', [ApiLessonController::class, 'showSingle']);
   Route::get('/lessons/group/{lessonGroup}/show', [ApiLessonController::class, 'showGroup']);

   Route::post('/lessons/my-make-ups/{makeUpLesson}/status', [ApiLessonController::class, 'statusMakeUp']);
});
// ------------ LESSONS ------------

// ------------ HISTORIES ------------

Route::middleware(['auth:sanctum'])->group(function () {

   Route::get('/lessons/teacher/history/index', [ApiLessonHistoryController::class, 'viewTeacher']);
   Route::get('/lessons/student/history/index', [ApiLessonHistoryController::class, 'viewStudent']);

   Route::get('/lessons/history/{history}/show', [ApiLessonHistoryController::class, 'show']);

   Route::post('/lessons/history/store', [ApiLessonHistoryController::class, 'store']);
   Route::delete('/lessons/history/{history}/delete', [ApiLessonHistoryController::class, 'delete']);

});
// ------------ HISTORIES ------------