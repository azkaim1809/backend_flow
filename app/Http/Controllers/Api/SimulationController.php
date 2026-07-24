<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlowConnection;
use App\Models\FlowNode;
use App\Models\NodeExecution;
use App\Models\Simulation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimulationController extends Controller
{
    use ApiResponse;

    // menampilkan keseluruhan data
    public function index($flow)
    {
        $simulations = Simulation::where('flow_id', $flow)
            ->select(
                'id',
                'flow_id',
                'status',
                'started_at',
                'completed_at',
                'input_data',
                'total_duration_ms',
                'created_at'
            )
            ->get();

        return $this->success($simulations, 'Data simulasi berhasil diambil');
    }

    // menambahkan data + otomatis bikin NodeExecution untuk node yang terhubung,
    // kasih jeda sebentar, LALU otomatis di-update jadi 'success' -- semua dalam 1 request.
    public function store(Request $request, $flow)
    {
        $request->validate([
            'status' => 'required|string|max:20',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'input_data' => 'nullable|array',
            'total_duration_ms' => 'nullable|integer',
        ]);

        // Ambil semua node dari flow ini, urut sesuai order_index.
        $flowNodes = FlowNode::where('flow_id', $flow)
            ->orderBy('order_index')
            ->get();

        if ($flowNodes->isEmpty()) {
            return $this->error('Flow ini belum punya node, simulasi tidak bisa dibuat', 422);
        }

        // Ambil semua koneksi milik flow ini.
        $connections = FlowConnection::where('flow_id', $flow)->get();
        $hasConnections = $connections->isNotEmpty();

        // Kumpulkan id node mana aja yang BENERAN ikut suatu koneksi.
        $connectedNodeIds = $connections->pluck('source_node_id')
            ->merge($connections->pluck('target_node_id'))
            ->unique();

        $connectedNodes = $flowNodes->whereIn('id', $connectedNodeIds);

        // ===== TAHAP 1: bikin Simulation + NodeExecution, status 'running' =====
        $simulation = DB::transaction(function () use ($request, $flow, $flowNodes, $hasConnections, $connectedNodes) {

            $simulation = Simulation::create([
                'flow_id' => $flow,
                'status' => $hasConnections ? 'running' : 'success',
                'started_at' => now(),
                'completed_at' => $hasConnections ? null : now(),
                'input_data' => $request->input_data,
                'total_duration_ms' => $request->total_duration_ms,
                'created_at' => now(),
            ]);

            if ($hasConnections) {
                foreach ($connectedNodes as $node) {
                    NodeExecution::create([
                        'simulation_id' => $simulation->id,
                        'flow_node_id' => $node->id,
                        'node_label' => $node->label,
                        'node_type' => $node->node_type,
                        'status' => 'running',
                        'input_data' => $node->input_params,
                    ]);
                }
                // 
            } else {
                // Gak ada koneksi -> cuma node pertama, langsung 'success'.
                $firstNode = $flowNodes->first();

                NodeExecution::create([
                    'simulation_id' => $simulation->id,
                    'flow_node_id' => $firstNode->id,
                    'node_label' => $firstNode->label,
                    'node_type' => $firstNode->node_type,
                    'status' => 'success',
                    'input_data' => $firstNode->input_params,
                    'executed_at' => now(),
                ]);
            }

            $completedAt = now();
                $durationMs = $simulation->started_at
                    ? (int) $simulation->started_at->diffInMilliseconds($completedAt)
                    : 0;

            $simulation->update([
                    'status' => 'success',
                    'completed_at' => $completedAt,
                    'total_duration_ms' => $durationMs,
                ]);

            return $simulation;
        });

    }
    // menampilkan data sesuai ID
    public function show($id)
    {
        $simulation = Simulation::with('nodeExecutions')->find($id);

        if (!$simulation) {
            return $this->error('Simulasi tidak ditemukan', 404);
        }

        return $this->success([
            'id' => $simulation->id,
            'flow_id' => $simulation->flow_id,
            'status' => $simulation->status,
            'started_at' => $simulation->started_at,
            'completed_at' => $simulation->completed_at,
            'input_data' => $simulation->input_data,
            'total_duration_ms' => $simulation->total_duration_ms,
            'created_at' => $simulation->created_at,
            'node_executions' => $simulation->nodeExecutions,
        ], 'Detail simulasi berhasil diambil');
    }

    // hapus simulasi
    public function destroy($id)
    {
        $simulation = Simulation::find($id);

        if (!$simulation) {
            return $this->error('Simulasi tidak ditemukan', 404);
        }

        $simulation->delete();

        return $this->success(null, 'Simulasi berhasil dihapus');
    }
}