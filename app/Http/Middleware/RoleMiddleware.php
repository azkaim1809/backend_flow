<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Admin = role_id 1
        if ($user->role_id != 1) {
            return response()->json([
                'message' => 'Forbidden. Hanya Admin yang dapat mengakses endpoint ini.'
            ], 403);
        }

        return $next($request);
    }
}