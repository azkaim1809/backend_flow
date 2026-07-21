<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoadUserPermissions
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            // Satu query gabungan: role + semua permission role itu + module tiap permission.
            // Dengan ini hasPermission()/permissionMap() dipanggil berkali-kali
            // di controller/route TIDAK query ulang ke DB.
            $user->load('role.permissions.module');
        }

        return $next($request);
    }
}