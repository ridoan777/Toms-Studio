<?php

use App\Helpers\Errors\ExceptionHandling;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
// use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        App\Providers\EnvLoadServiceProvider::class,
        App\Providers\SiteSettingsServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
    ])
    ->withRouting(
        channels: __DIR__ . '/../routes/channels.php',
        web: [
            __DIR__ . '/../routes/web.php',
            __DIR__ . '/../routes/Auth/web.auth.php',
            // ---------- custom made routes ----------
            ...require __DIR__ . '/../routes/_registry/custom_web_routes.php',
            // ----------------------------------------
        ],
        api: [
            __DIR__ . '/../routes/api.php',
            __DIR__ . '/../routes/Auth/api.auth.php',
            // ---------- custom made routes ----------
            ...require __DIR__ . '/../routes/_registry/custom_api_routes.php'
            // ----------------------------------------
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // custom-code
        $middleware->preventRequestForgery(except: [	// laravel 13
            '/api/*',
            '/stripe/*',
            '/api/stripe/*',
            '/revenuecat/*',
            '/broadcasting/*',
        ]);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'verify_role_key' => \App\Http\Middleware\System\RoleKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Illuminate\Foundation\Configuration\Exceptions $exceptions): void {
        // -------------------------- EXCEPTION::DEFAULT 401 --------------------------
        $exceptions->render(function (AuthenticationException $e, $request) {

            if ($request->expectsJson()) {
                return ExceptionHandling::runtimeExceptionFormat($request, 401, "Unauthenticated! Please login to continue", "AuthenticationException", $e->getMessage());
            }
            return redirect()->guest(route('login'))->with('error', 'Please login to continue.');
        });

        // -------------------------- EXCEPTION:: DEFAULT 403 --------------------------
        $exceptions->render(function (AuthorizationException $e, $request) {
            return ExceptionHandling::runtimeExceptionFormat($request, 403, "Unauthorized activity detected! Your current role does not have permission(s) to perform this action", "AuthorizationException", $e->getMessage());
        });

        // -------------------------- EXCEPTION:: 401 + DEFAULT 403 --------------------------
        $exceptions->render(function (UnauthorizedException $e, $request) {
            // normalizing Spatie UnauthorizedException into HttpException
            throw new HttpException(403, $e->getMessage(), $e);
        });

        $exceptions->render(function (HttpException $e, $request) {

            $status = $e->getStatusCode();

            if ($status === 403) {
                return ExceptionHandling::runtimeExceptionFormat($request, 403, "Unauthorized activity detected! Your current role does not have permission(s) to access or perform this action", "Spatie-Guard: UnauthorizedException!", $e->getMessage());
            }

            if ($status === 401) {
                return ExceptionHandling::runtimeExceptionFormat($request, 401, "You are either not logged-in or the user is not found.", "HttpException", $e->getMessage());
            }

            return null;
        });
        // -------------------------- EXCEPTION::THROTTLE --------------------------
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Too many requests. Please try again later.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                    'code' => 429
                ], 429);
            }

            return response()->view('System.Exceptions.throttle', [
                'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
            ], 429);
        });
        // ----------------- EXCEPTION::ROUTE-MODEL/NOT FOUND ID -----------------
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            $previous = $e->getPrevious();

            if ($previous instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return ExceptionHandling::handle('performing this action', $previous);
            }

            return ExceptionHandling::handle('performing this action', $e);
        });
    })->create();
