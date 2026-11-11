<?php

namespace App\Http\Middleware\System;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user){
            abort(401, 'Unauthenticated! You are either not logged-in or the user is not found.');
        }
        if (!$user->hasRoleKey(...$roles)){
            abort(403, 'Unauthorized!');
            // abort(403, 'Unauthorized! Your role has no access to proceed/perform this tasks');
        }
        return $next($request);
    }
}

// this is a custom middlewere file made to force controller to check role_key, instead of role name, as role names are mutable, role_keys are not.
/*
    USAGE: Use this inside middlewere constructor in controller:

    public static function middleware(): array
    {
      return [
        new Middleware(RoleMiddleware::using('super_admin|admin'), only: ['index']),
        new Middleware('verify_role_key:admin,student', only: ['index']),
      ];
    }

*/