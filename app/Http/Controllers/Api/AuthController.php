<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponse;

    //nambah user
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'role_id' => 1,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->success(new UserResource($user), 'Register berhasil', 201);
    }

    //login
    public function login(LoginRequest $request)
    {
        if (!$token = JWTAuth::attempt($request->validated())) {
            return $this->error('Email atau password salah', 401);
        }

        $user = JWTAuth::user();

        return $this->success([
            'token' => $token,
            'user' => new UserResource($user),
        ], 'Login berhasil');
    }
    // logout
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->success(null, 'Logout berhasil');
    }

    //profile
    public function profile()
    {
        return $this->success(new UserResource(JWTAuth::user()), 'Profile berhasil diambil');
    }

}