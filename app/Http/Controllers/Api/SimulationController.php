<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlowNode;
use App\Models\Simulation;
use App\Services\NodeExecutionService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimulationController extends Controller
{
    use ApiResponse;

    // batas pengaman biar tidak infinite loop kalau flow-nya ternyata siklik
    protected int $maxSteps = 100;

    public function __construct(protected NodeExecutionService $nodeExecutionService)
    {
    }

    // menampilkan keseluruhan data simulasi milik satu flow
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

    // menjalankan simulasi: mulai dari node pertama, evaluasi tiap kondisi,
    // ikuti cabang true/false sampai mentok -- hanya node yang BENERAN dilewati yang dicatat
    public function store(Request $request, $flow)
    {
        $request->validate([
            'input_data' => 'nullable|array',
        ]);

        $flowNodes = FlowNode::where('flow_id', $flow)
            ->orderBy('order_index')
            ->get();

        if ($flowNodes->isEmpty()) {
            return $this->error('Flow ini belum punya node, simulasi tidak bisa dibuat', 422);
        }

        $simulation = DB::transaction(function () use ($request, $flow, $flowNodes) {

            $simulation = Simulation::create([
                'flow_id' => $flow,
                'status' => 'running',
                'started_at' => now(),
                'input_data' => $request->input('input_data', []),
            ]);

            $context = $simulation->input_data ?? [];

            // node awal = order_index paling kecil
            $currentNode = $flowNodes->first();
            $overallStatus = 'success';
            $steps = 0;

            while ($currentNode && $steps < $this->maxSteps) {
                $execution = $this->nodeExecutionService->execute($currentNode, $simulation->id, $context);
                $steps++;

                if ($execution->status !== 'success') {
                    // node ini gagal (variabel hilang / expression error / koneksi tidak ditemukan)
                    // -> hentikan simulasi di sini, jangan lanjut ke node berikutnya
                    $overallStatus = 'failed';
                    $currentNode = null;
                    break;
                }

                $nextNodeId = $execution->output_data['next_node_id'] ?? null;

                if (!$nextNodeId) {
                    // tidak ada koneksi lanjutan -> flow selesai normal di titik ini
                    $currentNode = null;
                    break;
                }

                $currentNode = $flowNodes->firstWhere('id', $nextNodeId)
                    ?? FlowNode::find($nextNodeId);
            }

            if ($steps >= $this->maxSteps) {
                $overallStatus = 'failed';
            }

            $completedAt = now();
            $durationMs = (int) $simulation->started_at->diffInMilliseconds($completedAt);

            $simulation->update([
                'status' => $overallStatus,
                'completed_at' => $completedAt,
                'total_duration_ms' => $durationMs,
            ]);

            return $simulation;
        });

        return $this->success(
            $simulation->load('nodeExecutions'),
            'Simulasi berhasil dijalankan',
            201
        );
    }

    // menampilkan data sesuai ID, lengkap dengan riwayat eksekusi tiap node
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

    // finalisasi manual (opsional -- store() sudah otomatis menandai selesai,
    // ini dipakai kalau butuh override status/durasi secara manual)
    public function complete($id)
    {
        $simulation = Simulation::find($id);

        if (!$simulation) {
            return $this->error('Simulasi tidak ditemukan', 404);
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

        return $this->success($simulation, 'Simulasi ditandai selesai');
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