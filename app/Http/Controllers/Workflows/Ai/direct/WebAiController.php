<?php

namespace App\Http\Controllers\Workflows\Ai\direct;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Http, Log, Storage};
use Illuminate\Http\Request;
use Throwable;

// https://laracasts.com/series/fun-with-openai-and-laravel
// https://platform.openai.com/docs/api-reference/responses/create
class WebAiController extends Controller
{
   public function index()
   {
      return view('Admin.sidebar.Ai.index');
   }

   public function send(Request $request)
   {
      try {
         $response = Http::withToken(config('services.openai.chatgpt'))
            ->post(
               'https://api.openai.com/v1/responses',
               [
                  'model' => "gpt-4.1-mini",
                  // 'reasoning' => [
                  //    'effort' => "low"
                  // ],
                  'input' => [
                     [
                        'role' => "system",
                        'content' => "You are a gym & dietry trainer, skilled in training in complex situations. You consult with clients on their exercises and diet plan to help them get fit."
                     ],
                     [
                        'role' => "user",
                        'content' => "Generate a 1-day meal plan with breakfast, lunch & dinner including food's nutritions and amount (in gram), for an 85Kg, 172.72cm tall, 28 years old male who does 2-3 days weekly light exercise, and with no health conditions. The response for each day (like, day-1, day-2) and each sqaure or meal (like breakfast, lunch) should be enclosed in a key-value pair so that it can be extracted by the frontend. Return ONLY valid JSON. No explanations. No markdown."
                     ],
                  ],
               ]
            )->json('output.0.content.0.text');
            // dd($response);
            $json = $response;
         // $json = json_decode($response["output"][0]["content"][0]["text"], true);

         return view('Admin.sidebar.Ai.index', ['json' => $json])->with('success', "AI response has been geenrated!");
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
}
