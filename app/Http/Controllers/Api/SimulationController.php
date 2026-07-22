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

    // menambahkan data + otomatis bikin NodeExecution, TAPI cuma untuk node yang BENERAN terhubung
    public function store(Request $request, $flow)
    {
        $request->validate([
            'status' => 'required|string|max:20',
            'started_at' => 'required|date',
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

        // Kumpulkan id node mana aja yang BENERAN ikut suatu koneksi --
        // baik sebagai source_node_id maupun target_node_id.
        $connectedNodeIds = $connections->pluck('source_node_id')
            ->merge($connections->pluck('target_node_id'))
            ->unique();

        // Cuma node yang id-nya ada di daftar itu yang akan dieksekusi.
        // Node yang berdiri sendiri (gak nyambung ke koneksi manapun) di-skip.
        $connectedNodes = $flowNodes->whereIn('id', $connectedNodeIds);

        $simulation = DB::transaction(function () use ($request, $flow, $flowNodes, $hasConnections, $connectedNodes) {

            $simulation = Simulation::create([
                'flow_id' => $flow,
                // Kalau gak ada koneksi sama sekali, status dipaksa 'success' --
                // abaikan status yang dikirim client.
                'status' => $hasConnections ? $request->status : 'success',
                'started_at' => $request->started_at,
                'completed_at' => $hasConnections ? $request->completed_at : now(),
                'input_data' => $request->input_data,
                'total_duration_ms' => $request->total_duration_ms,
                'created_at' => now(),
            ]);

            if ($hasConnections) {
                // Cuma node yang benar-benar terhubung ke koneksi yang dibuatkan NodeExecution.
                // Node yang gak nyambung ke koneksi manapun di-skip, walau ada di flow yang sama.
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
            } else {
                // Gak ada koneksi sama sekali -> cuma node PERTAMA (order_index terkecil)
                // yang dibuatkan NodeExecution, dan langsung ditandai 'success'.
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

            return $simulation;
        });

        $simulation->load('nodeExecutions');

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
        ], 'Simulasi berhasil dibuat beserta node execution-nya', 201);
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

    // tandai simulasi selesai -- semua node_execution yang masih 'running'
    // diupdate jadi 'success', simulasi-nya juga diupdate jadi 'success'
    public function complete($id)
    {
        $simulation = Simulation::with('nodeExecutions')->find($id);

        if (!$simulation) {
            return $this->error('Simulasi tidak ditemukan', 404);
        }

        if ($simulation->status !== 'running') {
            return $this->error('Simulasi ini bukan sedang running, gak bisa di-complete', 422);
        }

        DB::transaction(function () use ($simulation) {

            // Update semua NodeExecution yang masih 'running' jadi 'success',
            // sekaligus catat waktu selesainya.
            $simulation->nodeExecutions()
                ->where('status', 'running')
                ->update([
                    'status' => 'success',
                    'executed_at' => now(),
                ]);

            // Hitung durasi total dari started_at sampai sekarang (dalam milidetik).
            $completedAt = now();

            $durationMs = $simulation->started_at
                ? (int) $simulation->started_at->diffInMilliseconds($completedAt)
                : 0;

            $simulation->update([
                'status' => 'success',
                'completed_at' => $completedAt,
                'total_duration_ms' => $durationMs,
            ]);
        });

        $simulation->refresh()->load('nodeExecutions');

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
        ], 'Simulasi berhasil ditandai selesai');
    }
}