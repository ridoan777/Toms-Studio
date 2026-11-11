<?php

use App\Http\Controllers\Communication\ApiChatController;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Route};


// ----------------- MESSAGES -----------------

Route::prefix('chatting')->middleware('auth:sanctum')->group(function () {
   
   Route::get('/get/users/index', [ApiChatController::class, 'allusers']);
   Route::post('/get/users/search', [ApiChatController::class, 'searchUser']);

   Route::get('/get/single/messages/{friend}', [ApiChatController::class, 'messages']);
   Route::get('/get/single/message/{message}/show', [ApiChatController::class, 'show']);
   
   Route::delete('/get/single/message/{message}/delete', [ApiChatController::class, 'delete']);

   Route::post('/send/single/messages', [ApiChatController::class, 'send']);

});
// ----------------- MESSAGES -----------------


// ----------------- GROUP -----------------

Route::prefix('chatting')->middleware(['auth:sanctum'])->group(function () {
   
   Route::get('/user/groups/index', [ApiChatController::class, 'indexAllGroups']);
   Route::get('/user/groups/my-groups/index', [ApiChatController::class, 'indexMyGroups']);

   Route::post('/user/group/new/store', [ApiChatController::class, 'storeGroup']);
   Route::get('/user/group/{chatGroup}/show', [ApiChatController::class, 'showGroup']);
   Route::delete('/user/group/{chatGroup}/delete', [ApiChatController::class, 'deleteGroup']);

   Route::post('/user/group/add-new-member', [ApiChatController::class, 'addNewMember']);

   Route::delete('/user/group/{group}/member/{member}/delete', [ApiChatController::class, 'removeMember']);

   Route::post('/send/group/messages', [ApiChatController::class, 'sendGroupMessage']);

});
// ----------------- GROUP -----------------


// ----------------- MANAGEMENT -----------------
Route::prefix('chatting')->middleware(['auth:sanctum'])->group(function () {
   
   Route::post('/user/{user}/block', [ApiChatController::class, 'block']);
   Route::post('/user/{user}/unblock', [ApiChatController::class, 'unblock']);
   Route::get('/user/blocklist/index', [ApiChatController::class, 'myBlocklist']);

   Route::post('/chat/{chat}/report', [ApiChatController::class, 'report']);

});
// ----------------- MANAGEMENT -----------------


// ----------------- VUE CHAT TEST -----------------

Route::prefix('frontend-test')->middleware(['auth:sanctum'])->group(function () {

   Route::get('/user', function (Request $request) {
      return Auth::user();
   });

   Route::get('/users', function () {
      $authId = Auth::id();
      return User::where('id', '!=', $authId)->select('id', 'name', 'email', 'avatar')->get();
   });

   Route::get('/users/{user}', function (User $user) {
      return $user->only(['id', 'name', 'email', 'avatar']);
   });
});
// ----------------- VUE CHAT TEST -----------------