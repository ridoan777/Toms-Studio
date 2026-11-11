<?php

use App\Http\Controllers\Workflows\Ai\agentic\ApiAiAgentController;
use App\Http\Controllers\Workflows\Ai\direct\ApiAiController;
use Illuminate\Support\Facades\Route;


// ------------ GENERAL ------------

// Route::middleware(['auth:sanctum'])->group(function () {

// });

Route::prefix('general')->middleware(['auth:sanctum'])->group(function () {

   Route::post('/ai/send', [ApiAiController::class, 'send']);
   Route::post('/ai/openrouter/send', [ApiAiController::class, 'openrouter']);
   Route::post('/ai/general/send', [ApiAiController::class, 'general']);

});
// ------------ GENERAL ------------

// ------------ AGENTIC ------------

Route::middleware(['auth:sanctum'])->group(function () {

   Route::post('/ai/agent/call', [ApiAiAgentController::class, 'callAgent']);
   // Route::post('/ai/agent/image/call', [ApiAiAgentController::class, 'callAgentForImage']);

});
// ------------ AGENTIC ------------