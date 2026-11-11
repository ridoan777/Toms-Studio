<?php

use App\Http\Controllers\Workflows\Lessons\{WebLessonController, WebGroupLessonController, WebLessonHistoryController};
use Illuminate\Support\Facades\Route;


// ------------ LESSONS ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {
   Route::get('/lessons/index', [WebLessonController::class, 'index'])->name('backend_lesson_index');

   Route::get('/lesson/create', [WebLessonController::class, 'create'])->name('backend_lesson_create');

   Route::get('/lesson/{singleLesson}/show', [WebLessonController::class, 'show'])->name('backend_lesson_show');

   Route::get('/lessons/{singleLesson}/dues', [WebLessonController::class, 'dues'])->name('backend_lesson_dues');

   Route::post('/lesson/store', [WebLessonController::class, 'store'])->name('backend_lesson_store');

   Route::post('/lesson/{lesson}/toggle', [WebLessonController::class, 'toggle'])->name('backend_lesson_toggle');

   Route::delete('/lesson/{lesson}/delete', [WebLessonController::class, 'delete'])->name('backend_lesson_delete');

   Route::post('/lessons/bulk-delete', [WebLessonController::class, 'bulkDelete'])->name('backend_lesson_bulk_delete');

   Route::post('/lesson/datatable', [WebLessonController::class, 'lessonDatatable'])->name('backend_lesson_datatable');
});
// ------------ LESSONS ------------

// ------------ GROUP LESSONS ------------

Route::prefix('admin/group')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {
   Route::get('/lessons/index', [WebGroupLessonController::class, 'index'])->name('backend_group_lesson_index');

   Route::get('/lesson/create', [WebGroupLessonController::class, 'create'])->name('backend_group_lesson_create');

   Route::get('/lesson/{lessonGroup}/show', [WebGroupLessonController::class, 'show'])->name('backend_group_lesson_show');

   Route::get('/lessons/{lessonGroup}/dues', [WebGroupLessonController::class, 'dues'])->name('backend_group_lesson_dues');

   Route::post('/lesson/store', [WebGroupLessonController::class, 'store'])->name('backend_group_lesson_store');

   Route::post('/lesson/{lessonGroup}/toggle', [WebGroupLessonController::class, 'toggle'])->name('backend_group_lesson_toggle');

   Route::delete('/lesson/{lessonGroup}/delete', [WebGroupLessonController::class, 'delete'])->name('backend_group_lesson_delete');

   Route::post('/lessons/bulk-delete', [WebGroupLessonController::class, 'bulkDelete'])->name('backend_group_lesson_bulk_delete');

   Route::post('/lesson/datatable', [WebGroupLessonController::class, 'groupLessonDatatable'])->name('backend_group_lesson_datatable');
});
// ------------ GROUP LESSONS ------------



// ------------ LESSON HISTORIES ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {
   Route::get('/lesson-histories/index', [WebLessonHistoryController::class, 'index'])->name('backend_lesson_histories_index');

   Route::get('/lesson-histories/{lessonId}/index', [WebLessonHistoryController::class, 'lessonIndex'])->name('backend_lesson_histories_lesson_index');

   Route::get('/lesson-histories/create', [WebLessonHistoryController::class, 'create'])->name('backend_lesson_histories_create');

   Route::get('/lesson-histories/{history}/show', [WebLessonHistoryController::class, 'show'])->name('backend_lesson_histories_show');

   Route::post('/lesson-histories/store', [WebLessonHistoryController::class, 'store'])->name('backend_lesson_histories_store');

   Route::post('/lesson-histories/{lesson}/toggle', [WebLessonHistoryController::class, 'toggle'])->name('backend_lesson_histories_toggle');

   Route::delete('/lesson-histories/{history}/delete', [WebLessonHistoryController::class, 'delete'])->name('backend_lesson_histories_delete');

   // Route::post('/lesson-histories/datatable', [WebLessonHistoryController::class, 'historyDatatable'])->name('backend_lesson_histories_datatable');
});
// ------------ LESSON HISTORIES ------------