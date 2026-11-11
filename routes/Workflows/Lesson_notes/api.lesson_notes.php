<?php

use App\Http\Controllers\Workflows\Lessons\ApiLessonNoteController;
use Illuminate\Support\Facades\Route;


// ------------ LESSONS ------------

Route::middleware(['auth:sanctum'])->group(function () {

   Route::get('/lesson-notes/my-notes/index', [ApiLessonNoteController::class, 'myNotes']);

   Route::get('/lesson-notes/{note}/show', [ApiLessonNoteController::class, 'show']);

   Route::post('/lesson-note/store', [ApiLessonNoteController::class, 'store']);

   Route::delete('/lesson-note/{note}/delete', [ApiLessonNoteController::class, 'delete']);
});
// ------------ LESSONS ------------