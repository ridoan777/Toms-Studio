<?php

namespace App\Helpers\Errors;

use BadMethodCallException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\{NotFoundHttpException, HttpException};
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Error;
// use FatalError;
use TypeError;
use ParseError;
use ArgumentCountError;
use ErrorException;
use Illuminate\Http\Exceptions\HttpResponseException;

/*
USE CASE-1:
   catch (\Throwable $e) {
      return ExceptionHandling::handle('updating profile', $e);
   }
USE CASE-2:
   return ExceptionHandling::bailout(403, "You are not authorized to do that task!");
USE CASE-3:
   Used this in app.php. Works for both Blade & API. Do not call it manually.
   return ExceptionHandling::runtimeExceptionFormat($request, 401, "Unauthenticated! Please login to continue", "AuthenticationException", $e->getMessage());
*/

class ExceptionHandling
{
   public static function runtimeExceptionFormat($request, int $status, string $message, ?string $type = null, ?string $error = null)
   {
      if ($request->expectsJson()) {
         return response()->json([
            'status' => false,
            'message' => $message,
            'type' => config('app.debug') ? ($type ?? null) : null,
            'error' => config('app.debug') ? $error : null,
            'code' => $status,
         ], $status);
      }
      return response()->view('System.Exceptions.general', [
         'code' => $status,
         'error_header' => $type ?? "Error",
         'error_throwable' => $error ?? null,
         'error_message' => $message ?? null,
      ], $status);
   }
   // -----------------------------------------
   public static function handle(?string $context = null, ?Throwable $e = null)
   {
      // ------------------------ EXCEPTION ------------------------
      if ($e instanceof HttpResponseException) {
         return $e->getResponse();
      }

      if ($e instanceof ValidationException) {
         $errors = $e->errors();

         $firstMessage = collect($errors)->flatten()->first() ?? "";

         return response()->json([
            'status' => false,
            'message' => "Validation error. {$firstMessage}",
            'type' => "Validation Exception",
            'errors' => config('app.debug') ? $e->errors() : null,
            'code' => 422
         ], 422);
      }

      // -----------------------------------------

      if ($e instanceof HttpException) {
         return response()->json([
            'status' => false,
            'message' => "Http error while {$context}.",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => $e->getStatusCode()
         ], $e->getStatusCode());
      }
      // ------------------- STRIPE EXCEPTION ----------------------
      if ($e instanceof \Stripe\Exception\CardException) {
         return response()->json([
            'status' => false,
            'message' => "Card error while {$context}. " . $e->getMessage(),
            'type' => "Stripe CardException",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 402
         ], 402);
      }
      // -----------------------------------------
      if ($e instanceof \Stripe\Exception\AuthenticationException) {
         return response()->json([
            'status' => false,
            'message' => "Stripe authentication failed while {$context}.",
            'type' => "Stripe AuthenticationException",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 401
         ], 401);
      }
      // -----------------------------------------
      if ($e instanceof \Stripe\Exception\RateLimitException) {
         return response()->json([
            'status' => false,
            'message' => "Too many requests to Stripe while {$context}.",
            'type' => "Stripe RateLimitException",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 429
         ], 429);
      }
      // -----------------------------------------
      if ($e instanceof \Stripe\Exception\InvalidRequestException) {
         return response()->json([
            'status' => false,
            'message' => "Invalid Stripe request while {$context}.",
            'type' => "Stripe InvalidRequestException",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 400
         ], 400);
      }
      // -----------------------------------------
      if ($e instanceof \Stripe\Exception\SignatureVerificationException) {
         return response()->json([
            'status' => false,
            'message' => "Invalid Stripe payload while {$context}.",
            'type' => "Stripe ApiErrorException",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 400
         ], 400);
      }
      // -----------------------------------------
      if ($e instanceof \Stripe\Exception\ApiConnectionException) {
         return response()->json([
            'status' => false,
            'message' => "Could not connect to Stripe while {$context}.",
            'type' => "Stripe ApiConnectionException",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 503
         ], 503);
      }
      // -----------------------------------------
      if ($e instanceof \Stripe\Exception\ApiErrorException) {
         return response()->json([
            'status' => false,
            'message' => "Stripe API error while {$context}.",
            'type' => "Stripe ApiErrorException",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 502
         ], 502);
      }

      // ------------------- STRIPE EXCEPTION ----------------------
      if ($e instanceof ModelNotFoundException) {
         return response()->json([
            'status' => false,
            'message' => "The Resource (or, content) not found while {$context}.",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 404
         ], 404);
      }
      // -----------------------------------------

      if ($e instanceof NotFoundHttpException) {
         return response()->json([
            'status' => false,
            'message' => "Resource not found while {$context}.",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 404
         ], 404);
      }
      // -----------------------------------------

      if ($e instanceof AuthorizationException) {
         return response()->json([
            'status' => false,
            'message' => "Unauthorized activity detected while {$context}.",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 403
         ], 403);
      }
      // -----------------------------------------

      if ($e instanceof QueryException) {
         return response()->json([
            'status' => false,
            'message' => "Database error while {$context}.",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 500
         ], 500);
      }
      // -----------------------------------------

      if ($e instanceof BadMethodCallException) {
         return response()->json([
            'status' => false,
            'message' => "Invalid method call while {$context}.",
            'type' => "Non-existent/Bad-method exception.",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 500
         ], 500);
      }
      // ------------------------ ERRORS ------------------------

      if ($e instanceof FatalError) {
         return response()->json([
            'status' => false,
            'message' => "Type mismatch while {$context}.",
            'type' => "TypeError",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 500
         ], 500);
      }
      // -----------------------------------------

      if ($e instanceof TypeError) {
         return response()->json([
            'status' => false,
            'message' => "Type mismatch while {$context}.",
            'type' => "TypeError",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 500
         ], 500);
      }
      // -----------------------------------------

      if ($e instanceof ArgumentCountError) {
         return response()->json([
            'status' => false,
            'message' => "Invalid arguments while {$context}.",
            'type' => "ArgumentCountError",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 500
         ], 500);
      }
      // -----------------------------------------

      if ($e instanceof ParseError) {
         return response()->json([
            'status' => false,
            'message' => "Syntax error while {$context}.",
            'type' => "ParseError",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 500
         ], 500);
      }
      // -----------------------------------------

      if ($e instanceof Error && $e->getMessage() !== '' && str_starts_with($e->getMessage(), 'Class "') && str_ends_with($e->getMessage(), ' not found')) {
         return response()->json([
            'status' => false,
            'message' => "Class not found while {$context}.",
            'type' => "Generic Error: ClassNotFoundError",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 500
         ], 500);
      }

      // ------------------- GENERICS ----------------------

      if ($e instanceof Error) {
         return response()->json([
            'status' => false,
            'message' => "Internal error while {$context}.",
            'type' => "PHP Error",
            'error' => config('app.debug') ? $e->getMessage() : null,
            'code' => 500
         ], 500);
      }
      // -----------------------------------------

      return response()->json([
         'status' => false,
         'message' => "Something went wrong while {$context}.",
         'type' => "Unknown exception",
         'error' => config('app.debug') ? $e->getMessage() : null,
         'code' => 500
      ], 500);
   }
   // -----------------------------------------
   public static function bailout(int $code = 400, string $message, $data = null): never
   {
      $request = request();
      if ($request->expectsJson()) {
         throw new HttpResponseException(
            $request->expectsJson()
            ? response()->json([
               'status' => false,
               'message' => $message,
               'code' => $code
            ], $code)
            : redirect()->back()->with('error', $message)
         );
      }
      abort($code, $message);
   }
}
