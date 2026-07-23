<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NodeExecution;

class NodeExecutionController extends Controller
{
    //hanya menampilkan
    public function index($simulation)
{
    $executions = NodeExecution::where('simulation_id', $simulation)
        ->select(
            'simulation_id',
            'flow_node_id',
            'node_label',
            'node_type',
            'status',
            'input_data',
            'output_data',
            'message',
            'duration_ms',
            'executed_at',
            'error_message'
        )
        ->get();

    return response()->json([
        'message' => 'Riwayat eksekusi node berhasil diambil',
        'data' => $executions
    ]);
}
}