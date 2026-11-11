<?php

namespace App\Http\Controllers\Workflows\Ai\agentic;

use App\Ai\Agents\ProjectAssistantAgent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Http, Log, Storage};
use Illuminate\Http\Request;
use Throwable;

class WebAiAgentController extends Controller
{
   public function callAgent(Request $request): string
   {
      $validated = $request->validate([
         'message' => 'required',
      ]);

      $USER = $request->user();

      $response = ProjectAssistantAgent::make(user: $USER)->prompt($validated['message']);

      return (string) $response;
   }
}
