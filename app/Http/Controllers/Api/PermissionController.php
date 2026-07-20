<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permissions;

class PermissionController extends Controller
{
        //GET
    public function index()
{
    $permissions = Permissions::with(['role', 'module'])->get();

    return response()->json([
        'message' => 'Data permission berhasil diambil',
        'data' => $permissions
    ]);
}
    //POST
    public function store(Request $request)
{
    $request->validate([
        'role_id' => 'required|exists:roles,id',
        'module_id' => 'required|exists:modules,id',
        'permission' => 'required|array'
    ]);

    $permission = Permissions::create([
        'role_id' => $request->role_id,
        'module_id' => $request->module_id,
        'permission' => $request->permission,
    ]);

    return response()->json([
        'message' => 'Permission berhasil ditambahkan',
        'data' => $permission
    ], 201);
}
    //SHOW
    public function show($id)
{
    $permission = Permissions::with(['role', 'module'])->find($id);

    if (!$permission) {
        return response()->json([
            'message' => 'Permission tidak ditemukan'
        ], 404);
    }

    return response()->json($permission);
}
    //UPDATE
    public function update(Request $request, $id)
{
    $permission = Permissions::find($id);

    if (!$permission) {
        return response()->json([
            'message' => 'Permission tidak ditemukan'
        ], 404);
    }

    $permission->update($request->all());

    return response()->json([
        'message' => 'Permission berhasil diupdate',
        'data' => $permission
    ]);
}
    //DELET
    public function destroy($id)
{
    $permission = Permissions::find($id);

    if (!$permission) {
        return response()->json([
            'message' => 'Permission tidak ditemukan'
        ], 404);
    }

    $permission->delete();

    return response()->json([
        'message' => 'Permission berhasil dihapus'
    ]);
}
}
