<?php

namespace App\Http\Controllers\Workflows\Ai\agentic;

use App\Ai\Agents\ProjectAssistantAgent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Http, Log, Storage};
use Illuminate\Http\Request;

use Throwable;

class ApiAiAgentController extends Controller
{
   public function callAgent(Request $request): array
   {
      $validated = $request->validate([
         'message' => 'required',
      ]);

      $USER = $request->user();

      $response = ProjectAssistantAgent::make(user: $USER)->prompt($validated['message']);

      $content = $response->text;

      $data = json_decode($content, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
         $clean = trim($content, "\" \n\r\t");
         $clean = stripslashes($clean);
         $data = json_decode($clean, true);
      }

      return [
         'reply' => $data,
         'tokens' => [
            'input' => $response->usage->promptTokens,
            'output' => $response->usage->completionTokens,
            'total_token' => $response->usage->promptTokens + $response->usage->completionTokens
         ],
         'metadata' => $response->meta
      ];
   }
}
