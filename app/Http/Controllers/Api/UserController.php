<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //ALL USER
    public function index()
{
    $users = User::select(
        'role_id',
        'name',
        'email',
        'created_at',
        'updated_at'
    )->get();

    return response()->json([
        'message' => 'Data user berhasil diambil',
        'data' => $users
    ]);
}
    //menambahkan user
    public function store(Request $request)
{
    $request->validate([
        'role_id' => 'required|exists:roles,id',
        'name' => 'required|string|max:100',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8'
    ]);

    $user = User::create([
        'role_id' => $request->role_id,
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
    'message' => 'User berhasil ditambahkan',
    'data' => [
        'role_id' => $user->role_id,
        'name' => $user->name,
        'email' => $user->email,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
    ]
], 201);
}
    //melihat user yang terdaftar berdasarkan ID
    public function show($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Data user berhasil diambil',
        'data' => [
            'role_id' => $user->role_id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]
    ]);
}
    //update data user
    public function update(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User tidak ditemukan'
        ], 404);
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

    return response()->json([
        'message' => 'User berhasil diperbarui',
        'data' => [
            'role_id' => $user->role_id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]
    ]);
}
    //delet data user berdasarkan ID
    public function destroy($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User tidak ditemukan'
        ], 404);
    }

    $user->delete();

    return response()->json([
        'message' => 'User berhasil dihapus'
    ]);
}
}
