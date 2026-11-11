<?php

use App\Http\Controllers\Workflows\Lessons\WebLessonNoteController;
use Illuminate\Support\Facades\Route;


// ------------ LESSON-NOTES ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {
   Route::get('/lesson-notes/index', [WebLessonNoteController::class, 'index'])->name('backend_lesson_note_index');

   Route::get('/lesson-note/create', [WebLessonNoteController::class, 'create'])->name('backend_lesson_note_create');

   Route::get('lesson-notes/get-students', [WebLessonNoteController::class, 'getStudents'])->name('backend_lesson_notes_get_students');

   Route::get('/lesson-note/{note}/show', [WebLessonNoteController::class, 'show'])->name('backend_lesson_note_show');

   Route::post('/lesson-note/store', [WebLessonNoteController::class, 'store'])->name('backend_lesson_note_store');

   Route::post('/lesson-note/{note}/toggle', [WebLessonNoteController::class, 'toggle'])->name('backend_lesson_note_toggle');

   Route::delete('/lesson-note/{note}/delete', [WebLessonNoteController::class, 'delete'])->name('backend_lesson_note_delete');

   Route::post('/lesson-notes/bulk-delete', [WebLessonNoteController::class, 'bulkDelete'])->name('backend_lesson_note_bulk_delete');

   Route::post('/lesson-note/datatable', [WebLessonNoteController::class, 'lessonNoteDatatable'])->name('backend_lesson_note_datatable');
});
// ------------ LESSON-NOTES ------------