<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Permission;


class PermissionController extends Controller
{
    use ApiResponse;

    // GET /permissions
    public function index()

    {
        $permissions = Permission::with(['role', 'module'])->get();


        return $this->success($permissions, 'Data permission berhasil diambil');
    }

    // POST /permissions
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'module_id' => 'required|exists:modules,id',
            'permission' => 'required|array',
        ]);


        $permission = Permission::create($validated);



        return $this->success($permission, 'Permission berhasil ditambahkan', 201);
    }

    // GET /permissions/{id}
    public function show($id)

    {
        $permission = Permission::with(['role', 'module'])->find($id);



        if (!$permission) {
            return $this->notFound('Permission tidak ditemukan');
        }

        return $this->success($permission, 'Detail permission berhasil diambil');
    }

    // PUT /permissions/{id}
    public function update(Request $request, $id)

    {
        $permission = Permission::find($id);



        if (!$permission) {
            return $this->notFound('Permission tidak ditemukan');
        }

        $validated = $request->validate([
            'role_id' => 'sometimes|exists:roles,id',
            'module_id' => 'sometimes|exists:modules,id',
            'permission' => 'sometimes|array',
        ]);

        $permission->update($validated);

        return $this->success($permission, 'Permission berhasil diupdate');
    }

    // DELETE /permissions/{id}
    public function destroy($id)

    {
        $permission = Permission::find($id);


        if (!$permission) {
            return $this->notFound('Permission tidak ditemukan');
        }

        $permission->delete();

        return $this->success(null, 'Permission berhasil dihapus');
    }
}