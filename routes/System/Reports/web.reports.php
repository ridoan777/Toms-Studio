<?php

use App\Http\Controllers\Users\WebUserController;
use App\Http\Controllers\Workflows\Projects\WebProjectController;
use Illuminate\Support\Facades\Route;

// ----------------- REPORTS::EXPORT -----------------
Route::prefix('admin/exports')->middleware('auth')->group(function () {

    Route::get('/reports/users/all-users/{fileType}', [WebUserController::class, 'exportExcelAllUsers'])->name('backend_users_spreadsheet_export');

    Route::get('/reports/projects/all-projects/{fileType}', [WebProjectController::class, 'exportProjects'])->name('backend_projects_spreadsheet_export');

});
// ----------------- REPORTS::EXPORT -----------------


// ----------------- REPORTS::IMPORT -----------------
Route::prefix('admin/imports')->middleware('auth')->group(function () {

    Route::post('/reports/users/all-users/excel', [WebUserController::class, 'importExcelAllUsers'])->name('backend_users_excel_import');

    Route::post('/reports/projects/all-projects/excel', [WebProjectController::class, 'importExcelAllProjects'])->name('backend_projects_excel_import');

});
// ----------------- REPORTS::IMPORT -----------------