<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    // Semua user
   public function index(Request $request)
{
    $query = User::select(
        'id',
        'role_id',
        'name',
        'email',
        'created_at',
        'updated_at'
    );

    if ($request->filled('name')) {
        $query->where('name', 'ILIKE', '%' . $request->name . '%');
    }

    if ($request->filled('email')) {
        $query->where('email', 'ILIKE', '%' . $request->email . '%');
    }

    $users = $query->get();

    return $this->success(
        $users,
        'Data user berhasil diambil'
    );
}

    // Menambahkan user
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->success([
            'id' => $user->id,
            'role_id' => $user->role_id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'User berhasil ditambahkan', 201);
    }

    // Menampilkan user berdasarkan ID
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        return $this->success([
            'id' => $user->id,
            'role_id' => $user->role_id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'Data user berhasil diambil');
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        $user->role_id = $request->role_id;
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return $this->success([
            'id' => $user->id,
            'role_id' => $user->role_id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'User berhasil diperbarui');
    }

    // Hapus user
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        $user->delete();

        return $this->success(
            null,
            'User berhasil dihapus'
        );
    }
}