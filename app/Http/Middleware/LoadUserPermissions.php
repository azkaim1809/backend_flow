<?php

namespace App\Http\Middleware;

<<<<<<< HEAD
=======
use App\Models\User;
>>>>>>> c28c76181dccc689ddd544bf9542692ab33b05be
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoadUserPermissions
{
    public function handle(Request $request, Closure $next): Response
    {
<<<<<<< HEAD
        $user = Auth::user();

        if ($user) {
            // Satu query gabungan: role + semua permission role itu + module tiap permission.
            // Dengan ini hasPermission()/permissionMap() dipanggil berkali-kali
            // di controller/route TIDAK query ulang ke DB.
=======
        /** @var User|null $user */
        $user = Auth::user();

        if ($user instanceof User) {
>>>>>>> c28c76181dccc689ddd544bf9542692ab33b05be
            $user->load('role.permissions.module');
        }

        return $next($request);
    }
}