<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Simulation;

class SimulationController extends Controller
{
    //menampilkan keseluruhan data
    public function index($flow)
{
    $simulations = Simulation::where('flow_id', $flow)
        ->select(
            'flow_id',
            'status',
            'started_at',
            'completed_at',
            'input_data',
            'total_duration_ms',
            'created_at'
        )
        ->get();

    return response()->json([
        'message' => 'Data simulasi berhasil diambil',
        'data' => $simulations
    ]);
}

    //menambahkan data
    public function store(Request $request, $flow)
{
    $request->validate([
        'status' => 'required|string|max:20',
        'started_at' => 'required|date',
        'completed_at' => 'nullable|date',
        'input_data' => 'nullable|array',
        'total_duration_ms' => 'nullable|integer',
    ]);

    $simulation = Simulation::create([
        'flow_id' => $flow,
        'status' => $request->status,
        'started_at' => $request->started_at,
        'completed_at' => $request->completed_at,
        'input_data' => $request->input_data,
        'total_duration_ms' => $request->total_duration_ms,
        'created_at' => now(),
    ]);

    return response()->json([
        'message' => 'Simulasi berhasil dibuat',
        'data' => [
            'flow_id' => $simulation->flow_id,
            'status' => $simulation->status,
            'started_at' => $simulation->started_at,
            'completed_at' => $simulation->completed_at,
            'input_data' => $simulation->input_data,
            'total_duration_ms' => $simulation->total_duration_ms,
            'created_at' => $simulation->created_at,
        ]
    ], 201);
}

    // menampilkan data sesuai ID
    public function show($id)
{
    $simulation = Simulation::find($id);

    if (!$simulation) {
        return response()->json([
            'message' => 'Simulasi tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Detail simulasi berhasil diambil',
        'data' => [
            'flow_id' => $simulation->flow_id,
            'status' => $simulation->status,
            'started_at' => $simulation->started_at,
            'completed_at' => $simulation->completed_at,
            'input_data' => $simulation->input_data,
            'total_duration_ms' => $simulation->total_duration_ms,
            'created_at' => $simulation->created_at,
        ]
    ]);
}

}
