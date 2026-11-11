<?php

use App\Http\Controllers\Workflows\Positions\WebInstrumentController;
use Illuminate\Support\Facades\Route;


// ------------ INSTRUMENTS ------------

Route::prefix('admin')->middleware(['auth:web', 'verify_role_key:admin,super_admin'])->group(function () {
   Route::get('/instruments/index', [WebInstrumentController::class, 'index'])->name('backend_instrument_index');

   Route::get('/instruments/create', [WebInstrumentController::class, 'create'])->name('backend_instrument_create');

   Route::get('/instruments/{instrument}/show', [WebInstrumentController::class, 'show'])->name('backend_instrument_show');

   Route::post('/instruments/store', [WebInstrumentController::class, 'store'])->name('backend_instrument_store');

   Route::post('/instruments/{instrument}/toggle', [WebInstrumentController::class, 'toggle'])->name('backend_instrument_toggle');


   Route::delete('/instruments/{instrument}/delete', [WebInstrumentController::class, 'delete'])->name('backend_instrument_delete');

   Route::post('/instruments/bulk-delete', [WebInstrumentController::class, 'bulkDelete'])->name('backend_instrument_bulk_delete');

   Route::post('/instruments/datatable', [WebInstrumentController::class, 'instrumentDatatable'])->name('backend_instrument_datatable');
});
// ------------ INSTRUMENTS ------------