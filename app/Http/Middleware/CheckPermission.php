<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $module, string $action): Response
    {
        $user = Auth::user();

        if (!$user || !$user->hasPermission($module, $action)) {
            return response()->json([
                'message' => "Tidak punya izin: {$module}.{$action}",
            ], 403);
        }

        return $next($request);
    }
}