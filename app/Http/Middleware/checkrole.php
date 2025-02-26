<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$roles
     * @return Response
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Check if the user is authenticated and has the required role
        if (auth()->user() && in_array(auth()->user()->role, $roles)) {
            return $next($request);
        } elseif (!auth()->user()) {
            // If the user is not authenticated
            return response()->json([
                "message" => 'Please log in'
            ], 401); // 401 Unauthorized
        } else {
            // If the user is authenticated but does not have the required role
            return response()->json([
                "message" => 'You are not authorized to perform this action'
            ], 403); // 403 Forbidden
        }
    }
}
