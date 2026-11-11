<?php

use App\Http\Controllers\Workflows\Ai\agentic\WebAiAgentController;
use App\Http\Controllers\Workflows\Ai\WebAiController;
use Illuminate\Support\Facades\Route;


// ------------ SDK AGENTS ------------

Route::prefix('admin')->group(function () {

   Route::get('/ai/agent/call', [WebAiAgentController::class, 'callAgent'])->name('backend_ai_agent');

});
// ------------ SDK AGENTS ------------