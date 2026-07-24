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
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\PasswordResetToken;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;

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

        //ubah Password
        public function changePassword(ChangePasswordRequest $request)
    {
        $user = JWTAuth::user();

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama tidak sesuai'
            ], 400);
        }

        // Update password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }

     // ===============================
    // FITUR LUPA PASSWORD
    // ===============================

            public function forgotPassword(ForgotPasswordRequest $request)
        {
            // Cek apakah email terdaftar
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Email tidak ditemukan.'
                ], 404);
            }

            // Generate OTP 6 digit
            $otp = random_int(100000, 999999);

            // Hapus OTP lama jika ada
            PasswordResetToken::where('email', $request->email)->delete();

            // Simpan OTP baru (DI-HASH)
            PasswordResetToken::create([
                'email' => $request->email,
                'otp' => Hash::make($otp),
                'expired_at' => now()->addMinutes(5),
            ]);

            // Kirim OTP ke email
            Mail::to($request->email)->send(new SendOtpMail($otp));

            return response()->json([
                'message' => 'Kode OTP berhasil dikirim ke email.'
            ]);
        }

           public function resetPassword(ResetPasswordRequest $request)
            {
                // Cari OTP berdasarkan email
                $resetToken = PasswordResetToken::where('email', $request->email)->first();

                if (!$resetToken) {
                    return response()->json([
                        'message' => 'OTP tidak valid.'
                    ], 400);
                }

                // Cek OTP yang di-hash
                if (!Hash::check($request->otp, $resetToken->otp)) {
                    return response()->json([
                        'message' => 'OTP tidak valid.'
                    ], 400);
                }

                // Cek masa berlaku OTP
                if ($resetToken->expired_at->isPast()) {
                    return response()->json([
                        'message' => 'OTP sudah kadaluarsa.'
                    ], 400);
                }

                // Cari user
                $user = User::where('email', $request->email)->first();

                if (!$user) {
                    return response()->json([
                        'message' => 'User tidak ditemukan.'
                    ], 404);
                }

                // Update password
                $user->password = Hash::make($request->password);
                $user->save();

                // Hapus OTP agar tidak bisa dipakai lagi
                $resetToken->delete();

                return response()->json([
                    'message' => 'Password berhasil direset.'
                ]);
            }
}