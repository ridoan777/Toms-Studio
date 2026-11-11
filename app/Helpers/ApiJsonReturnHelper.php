<?php

namespace App\Helpers;

class ApiJsonReturnHelper
{
   public static function handle(bool $STATUS = false, int $CODE = 500, string $MESSAGE = "unresolved message from ApiJsonReturnHelper", $DATA = null)
   {
      return response()->json([
         'status' => $STATUS,
         'message' => $MESSAGE,
         'data' => $DATA,
         'code' => $CODE
      ], $CODE);
   }
}

/*
USE CASE-1:
   return ApiJsonReturnHelper::handle(false, 401, 'Data fetched failed.', $data);

USE CASE-2:
   return ApiJsonReturnHelper::handle(false, 409, 'You have made this content.', [
      'user' => $user,
      'content' => $content,
   ]);
USE CASE-3:
   try {
      return ApiJsonReturnHelper::handle(false, 401, 'Data fetched failed.', $data);
   } catch (\Throwable $e) {
      return ExceptionHandling::handle('updating profile', $e);
   }
*/