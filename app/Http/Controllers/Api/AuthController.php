<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
       $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'role_id' => 1,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

       

        return response()->json([
        'message' => 'Register berhasil',
        'user' => [
        'name' => $user->name,
        'email' => $user->email,
        'role_id' => $user->role_id,
    ]
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json([
            'message' => 'Email atau Password salah'
        ], 401);
    }

    $user = JWTAuth::user();

    return response()->json([
        'message' => 'Login berhasil',
        'token' => $token,
        'user' => [
            'role_id' => $user->role_id,
            'name' => $user->name,
            'email' => $user->email,
        ]
    ]);
}

    // LOGOUT
     public function logout()
{
    JWTAuth::invalidate(JWTAuth::getToken());

    return response()->json([
        'message' => 'Logout berhasil'
    ]);
}
    public function profile()
    {
        return response()->json([
            'message' => 'Profile berhasil diambil',
            'user' => JWTAuth::user()
        ]);
    }

        //PERMISSIONS
       public function permissions()
{
    try {
        $user = JWTAuth::user(); // otomatis terisi karena middleware sudah validasi token

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $permissions = DB::table('permissions as p')
            ->join('roles as r', 'p.role_id', '=', 'r.id')
            ->join('modules as m', 'p.module_id', '=', 'm.id')
            ->where('r.id', $user->role_id)
            ->select('r.name as role', 'm.name as module', 'p.permission')
            ->orderBy('m.id')
            ->get();

        return response()->json([
            'message' => 'Permission user berhasil diambil',
            'user' => $user->name,
            'data' => $permissions
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
}
}