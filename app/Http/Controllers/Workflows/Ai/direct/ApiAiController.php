<?php

namespace App\Http\Controllers\Workflows\Ai\direct;

use App\Helpers\ApiJsonReturnHelper;
use App\Helpers\Errors\ExceptionHandling;
use App\Http\Controllers\Controller;
// use Http;
use App\Models\System\Settings\OptionSiteSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;


class ApiAiController extends Controller
{
   public function send(Request $request)
   {
      $OPEN_AI_DB = OptionSiteSetup::where('type', 'open_ai')->pluck('value', 'name')->toArray();
      try {
         $start = microtime(true);
         $response = Http::withToken(config('services.openai.chatgpt'))
            ->timeout((int)($OPEN_AI_DB['timeout_seconds'] ?? 120))
            ->post(
               $OPEN_AI_DB['api_endpoint'],
               [
                  'model' => $OPEN_AI_DB['ai_model'] ?? 'gpt-4.1-mini',
                  'max_output_tokens' => (int)($OPEN_AI_DB['max_output_tokens'] ?? 9999),
                  'text' => [
                     'format' => [
                        'type' => 'json_object'
                     ]
                  ],
                  'input' => [
                     [
                        'role' => "system",
                        'content' => $OPEN_AI_DB['role_system_content'] ?? 'You are a property and transaction management virtual assistance.'
                     ],
                     [
                        'role' => 'user',
                        'content' => "
                           Return ONLY valid JSON.

                           Format:
                           {
                              \"story\": \"string\"
                           }

                           Make a 50 word story about your role in my system.
                        ",
                     ],
                  ],
               ]
            )->json();
         $end = microtime(true);
         if (isset($response['error'])) {
            return response()->json([
               'status' => false,
               'error_type' => 'ai_rate_limit',
               'message' => $response['error']['message'],
               'retry_after' => 'later',
               'code' => 409
            ], 409);
         }

         $timeSpent = round($end - $start, 3);

         $text = $response['output'][0]['content'][0]['text'] ?? null;

         $planning = json_decode($text, true);

         if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON returned by OpenAI');
         }

         $tokenUsage = $response['usage'] ?? null;

         return [
            'status' => true,
            'data' => [
               // 'response' => $response,
               'planning' => $planning,
               'tokenUsage' => $tokenUsage,
               'timeSpentSeconds' => $timeSpent,
               'ai_model' => $OPEN_AI_DB['ai_model'] ?? null,
               'key' => config('services.openai.chatgpt')
            ],
            'code' => 200
         ];
      } catch (Throwable $e) {
         return ExceptionHandling::handle('communication with AI server(s)', $e);
      }
   }
   // -----------------------------------------------

   public function send1(Request $request)
   {
      try {
         $response = Http::withHeaders(
            [
               "Content-Type" => "application/json",
               "Authorization" => "Bearer " . config('services.openai.chatgpt')
            ]
         )->post("https://api.openai.com/v1/responses", [
                  "model" => "gpt-4.1-mini",
                  "input" => $request->post('content'),
               ])->json();

         // return $response['choices'][0]['message']['content'];
         // return $response['output_texts'];
         return response()->json([
            'status' => true,
            'data' => $response["output"][0]["content"][0]["text"] ?? null,
            'code' => 200
         ]);
      } catch (Throwable $e) {

         return response()->json([
            'status' => false,
            'data' => $response ?? null,
            "error" => $e->getMessage(),
            "details" => $e->getTrace()[0] ?? null,
            "code" => 500
         ], 500);
      }
   }
   // -----------------------------------------------

   public function openrouter(Request $request)
   {
      try {
         $USER = $request->user();

         $validated = $request->validate([
            'message' => 'required|string|max:2000'
         ]);

         $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
            'HTTP-Referer' => config('app.url'), // optional
            'X-OpenRouter-Title' => config('app.name'), // optional
         ])->post('https://openrouter.ai/api/v1/chat/completions', [
                  'model' => 'openai/gpt-5.2',
                  'messages' => [
                     [
                        'role' => 'user',
                        'content' => $validated['message']
                     ]
                  ]
               ]);

         $result = $response->json();

         return ApiJsonReturnHelper::handle(true, 200, "AI response has been received sucessfully!", $result);

      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching AI response', $e);
      }
   }
   // -----------------------------------------------

   public function general(Request $request)
   {
      try {
         $USER = $request->user();

         $validated = $request->validate([
            'message' => 'required|string|max:2000'
         ]);

         $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
            'HTTP-Referer' => config('app.url'), // optional
            'X-OpenRouter-Title' => config('app.name'), // optional
         ])->post('https://api.groq.com/openai/v1/chat/completions', [
                  // 'model' => 'groq/compound',
                  'model' => 'openai/gpt-oss-120b',
                  // 'model' => 'meta-llama/llama-4-scout-17b-16e-instruct',
                  'messages' => [
                     [
                        'role' => 'user',
                        'content' => "
                           {$validated['message']}

                           RULES (STRICT):
                           - Return ONLY valid JSON
                           - Do NOT include any explanation, text, or markdown
                           - Do NOT wrap JSON in quotes
                           - Do NOT escape JSON (no \\n, no \\\")
                           - Use DOUBLE quotes for all keys and values
                           - No trailing commas
                           - No comments

                           FORMAT:
                           {
                              \"key_1\": \"value_1\",
                              \"key_2\": \"value_2\",
                              \"key_3\": {
                                 \"key_3_a\": \"value_3_a\",
                                 \"key_3_b\": \"value_3_b\"
                              }
                           }

                           If you cannot comply, return:
                           {
                              \"error\": \"Invalid response\"
                           }
                        "
                     ]
                  ]
               ]);

         // $result = $response->json();
         $content = $response['choices'][0]['message']['content'];

         $data = json_decode($content, true);

         if (json_last_error() !== JSON_ERROR_NONE) {
            // handle error
         }

         return ApiJsonReturnHelper::handle(true, 200, "AI response has been received sucessfully!", $data);

      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching AI response', $e);
      }
   }
}
