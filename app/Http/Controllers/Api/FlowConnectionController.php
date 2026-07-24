<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowConnection;
use App\Traits\ApiResponse;

class FlowConnectionController extends Controller
{
    use ApiResponse;

    // Menampilkan seluruh koneksi berdasarkan flow
    public function index($flow)
    {
        $connections = FlowConnection::where('flow_id', $flow)
            ->select(
                'id',
                'flow_id',
                'source_node_id',
                'target_node_id',
                'branch_label',
                'created_at'
            )
            ->get();

        return $this->success(
            $connections,
            'Data koneksi berhasil diambil'
        );
    }

    // Menambahkan koneksi
    public function store(Request $request, $flow)
    {
        $request->validate([
            'source_node_id' => 'required|exists:flow_nodes,id',
            'target_node_id' => 'required|exists:flow_nodes,id',
            'branch_label' => 'nullable|string|max:50',
        ]);

        $connection = FlowConnection::create([
            'flow_id' => $flow,
            'source_node_id' => $request->source_node_id,
            'target_node_id' => $request->target_node_id,
            'branch_label' => $request->branch_label,
            'created_at' => now(),
        ]);

        return $this->success([
            'id' => $connection->id,
            'flow_id' => $connection->flow_id,
            'source_node_id' => $connection->source_node_id,
            'target_node_id' => $connection->target_node_id,
            'branch_label' => $connection->branch_label,
            'created_at' => $connection->created_at,
        ], 'Koneksi berhasil ditambahkan', 201);
    }

    // Menampilkan koneksi berdasarkan ID
    public function show($id)
    {
        $connection = FlowConnection::find($id);

        if (!$connection) {
            return $this->notFound('Koneksi tidak ditemukan');
        }

        return $this->success([
            'id' => $connection->id,
            'flow_id' => $connection->flow_id,
            'source_node_id' => $connection->source_node_id,
            'target_node_id' => $connection->target_node_id,
            'branch_label' => $connection->branch_label,
            'created_at' => $connection->created_at,
        ], 'Data koneksi berhasil diambil');
    }

    // Update koneksi
    public function update(Request $request, $id)
    {
        $connection = FlowConnection::find($id);

        if (!$connection) {
            return $this->notFound('Koneksi tidak ditemukan');
        }

        $request->validate([
            'source_node_id' => 'required|exists:flow_nodes,id',
            'target_node_id' => 'required|exists:flow_nodes,id',
            'branch_label' => 'nullable|string|max:50',
        ]);

        $connection->update([
            'source_node_id' => $request->source_node_id,
            'target_node_id' => $request->target_node_id,
            'branch_label' => $request->branch_label,
        ]);

        return $this->success([
            'id' => $connection->id,
            'flow_id' => $connection->flow_id,
            'source_node_id' => $connection->source_node_id,
            'target_node_id' => $connection->target_node_id,
            'branch_label' => $connection->branch_label,
            'created_at' => $connection->created_at,
        ], 'Koneksi berhasil diperbarui');
    }

    // Hapus koneksi
    public function destroy($id)
    {
        $connection = FlowConnection::find($id);

        if (!$connection) {
            return $this->notFound('Koneksi tidak ditemukan');
        }

        $connection->delete();

        return $this->success(
            null,
            'Koneksi berhasil dihapus'
        );
    }
}