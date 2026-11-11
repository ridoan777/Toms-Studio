<?php

use App\Http\Controllers\Workflows\Projects\WebProjectController;
use Illuminate\Support\Facades\Route;


// ------------ PROJECTS ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {
   Route::get('/project/index', [WebProjectController::class, 'index'])->name('backend_project_index');

   Route::get('/project/create', [WebProjectController::class, 'create'])->name('backend_project_create');

   Route::get('/project/{singleProject}/show', [WebProjectController::class, 'show'])->name('backend_project_show');


   Route::post('/project/store', [WebProjectController::class, 'store'])->name('backend_project_store');

   Route::post('/project/{project}/toggle', [WebProjectController::class, 'toggle'])->name('backend_project_toggle');


   Route::delete('/project/{project}/delete', [WebProjectController::class, 'delete'])->name('backend_project_delete');

   Route::post('/projects/bulk-delete', [WebProjectController::class, 'bulkDelete'])->name('backend_project_bulk_delete');


   Route::post('/project/datatable', [WebProjectController::class, 'projectDatatable'])->name('backend_project_datatable');
});
// ------------ PROJECTS ------------