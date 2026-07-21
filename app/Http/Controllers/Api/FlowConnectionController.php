<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowConnection;

class FlowConnectionController extends Controller
{
    //menampilkan keseluruhan
    public function index($flow)
{
    $connections = FlowConnection::where('flow_id', $flow)
        ->select(
            'flow_id',
            'source_node_id',
            'target_node_id',
            'branch_label',
            'created_at'
        )
        ->get();

    return response()->json([
        'message' => 'Data koneksi berhasil diambil',
        'data' => $connections
    ]);
}

    //manambahkan
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

    return response()->json([
        'message' => 'Koneksi berhasil ditambahkan',
        'data' => [
            'flow_id' => $connection->flow_id,
            'source_node_id' => $connection->source_node_id,
            'target_node_id' => $connection->target_node_id,
            'branch_label' => $connection->branch_label,
            'created_at' => $connection->created_at,
        ]
    ], 201);
}

    //menampilkan sesuai ID
    public function show($id)
{
    $connection = FlowConnection::find($id);

    if (!$connection) {
        return response()->json([
            'message' => 'Koneksi tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Data koneksi berhasil diambil',
        'data' => [
            'flow_id' => $connection->flow_id,
            'source_node_id' => $connection->source_node_id,
            'target_node_id' => $connection->target_node_id,
            'branch_label' => $connection->branch_label,
            'created_at' => $connection->created_at,
        ]
    ]);
}

    //update data
    public function update(Request $request, $id)
{
    $connection = FlowConnection::find($id);

    if (!$connection) {
        return response()->json([
            'message' => 'Koneksi tidak ditemukan'
        ], 404);
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

    return response()->json([
        'message' => 'Koneksi berhasil diperbarui',
        'data' => [
            'flow_id' => $connection->flow_id,
            'source_node_id' => $connection->source_node_id,
            'target_node_id' => $connection->target_node_id,
            'branch_label' => $connection->branch_label,
            'created_at' => $connection->created_at,
        ]
    ]);
}

    //delet
    public function destroy($id)
{
    $connection = FlowConnection::find($id);

    if (!$connection) {
        return response()->json([
            'message' => 'Koneksi tidak ditemukan'
        ], 404);
    }

    $connection->delete();

    return response()->json([
        'message' => 'Koneksi berhasil dihapus'
    ]);
}
}
