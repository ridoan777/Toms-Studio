<?php

// use App\Events\MessageSent;
use App\Http\Controllers\Communication\WebChatController;
use Illuminate\Support\Facades\Route;

// --------------------- MESSENGER ---------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {
   
   Route::get('/communications/messenger/index', [WebChatController::class, 'messengerIndex'])->name('backend_messenger_index');

   Route::post('/send/single/messages', [WebChatController::class, 'send'])->name('backend_message_send');

   Route::post('/communications/messenger/{message}/delete', [WebChatController::class, 'deleteMyMessage'])->name('backend_my_message_delete');
   
});
// --------------------- MESSENGER ---------------------


// --------------------- BLOCKLIST ---------------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
   
   Route::get('/communications/chat/blocklist', [WebChatController::class, 'blockIndex'])->name('backend_chat_blocklist_index');
   
   Route::post('/communications/chat/{block}/toggle', [WebChatController::class, 'blockToggle'])->name('backend_chat_block_toggle');

   Route::delete('/communications/chat/{block}/delete', [WebChatController::class, 'blockDelete'])->name('backend_chat_block_delete');

   Route::post('/communications/chat//datatable', [WebChatController::class, 'blocklistDatatable'])->name('backend_chat_blocklist_datatable');
});

// --------------------- DISPUTE MANAGEMENT ---------------------
Route::prefix('admin')->middleware(['auth:web'])->group(function () {
   
   Route::get('/communications/chat/reports/index', [WebChatController::class, 'reportIndex'])->name('backend_chat_report_index');
   
   // Route::get('/communications/chat/reports/{report}/show', [WebChatController::class, 'reportShow'])->name('backend_chat_report_show');
   

   Route::post('/communications/chat/reports/{report}/handle', [WebChatController::class, 'reportHandle'])->name('backend_chat_report_handle');
      
   // Route::post('/communications/chat/report/{report}/toggle', [WebChatController::class, 'reportToggle'])->name('backend_displute_report_show');

   
   // Route::delete('/communications/chat/report/{report}/delete', [WebChatController::class, 'reportDelete'])->name('backend_displute_report_delete');

   Route::post('/communications/chat/reports/datatable', [WebChatController::class, 'chatReportsDatatable'])->name('backend_chat_reports_datatable');
});
// --------------------- DISPUTE MANAGEMENT ---------------------
