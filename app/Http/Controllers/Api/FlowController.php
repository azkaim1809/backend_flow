<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flow;
use App\Traits\ApiResponse;

class FlowController extends Controller
{
    use ApiResponse;

    // Semua flow
      public function index(Request $request)
    {
        $flows = Flow::select(
            'id',
            'name',
            'description',
            'version',
            'status',
            'created_at',
            'updated_at'
        );

        if ($request->filled('name')) {
            $flows->where('name', 'ILIKE', '%' . $request->name . '%');
        }

        $flows = $flows->get();

        if ($flows->isEmpty()) {
            return $this->success([], 'Data flow tidak ditemukan');
        }

        return $this->success(
            $flows,
            'Data flow berhasil diambil'
        );
    }

    // Menambahkan
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'version' => 'required|string|max:20',
            'status' => 'required|string|max:20',
        ]);

        $flow = Flow::create([
            'name' => $request->name,
            'description' => $request->description,
            'version' => $request->version,
            'status' => $request->status,
        ]);

        return $this->success([
            'id' => $flow->id,
            'name' => $flow->name,
            'description' => $flow->description,
            'version' => $flow->version,
            'status' => $flow->status,
            'created_at' => $flow->created_at,
            'updated_at' => $flow->updated_at,
        ], 'Flow berhasil ditambahkan', 201);
    }

    // Melihat berdasarkan ID
    public function show($id)
    {
        $flow = Flow::find($id);

        if (!$flow) {
            return $this->notFound('Flow tidak ditemukan');
        }

        return $this->success([
            'id' => $flow->id,
            'name' => $flow->name,
            'description' => $flow->description,
            'version' => $flow->version,
            'status' => $flow->status,
            'created_at' => $flow->created_at,
            'updated_at' => $flow->updated_at,
        ], 'Data flow berhasil diambil');
    }

    // Update berdasarkan ID
    public function update(Request $request, $id)
    {
        $flow = Flow::find($id);

        if (!$flow) {
            return $this->notFound('Flow tidak ditemukan');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'version' => 'required|string|max:20',
            'status' => 'required|string|max:20',
        ]);

        $flow->update([
            'name' => $request->name,
            'description' => $request->description,
            'version' => $request->version,
            'status' => $request->status,
        ]);

        return $this->success([
            'id' => $flow->id,
            'name' => $flow->name,
            'description' => $flow->description,
            'version' => $flow->version,
            'status' => $flow->status,
            'created_at' => $flow->created_at,
            'updated_at' => $flow->updated_at,
        ], 'Flow berhasil diperbarui');
    }

    // Hapus
    public function destroy($id)
    {
        $flow = Flow::find($id);

        if (!$flow) {
            return $this->notFound('Flow tidak ditemukan');
        }

        $flow->delete();

        return $this->success(
            null,
            'Flow berhasil dihapus'
        );
    }
}