<?php

namespace App\Services;

use App\Models\FlowConnection;
use App\Models\FlowNode;
use App\Models\NodeExecution;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class NodeExecutionService
{
    protected ExpressionLanguage $expressionLanguage;

    protected array $reservedWords = ['true', 'false', 'null', 'and', 'or', 'not', 'in', 'matches'];

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * Eksekusi satu node, lalu tentukan node berikutnya (kalau ada).
     *
     * @param FlowNode $node
     * @param int $simulationId
     * @param array $context variabel tambahan (mis. simulations.input_data), contoh: ['stock' => 45]
     */
    public function execute(FlowNode $node, int $simulationId, array $context = []): NodeExecution
    {
        $start = microtime(true);

        $variables = array_merge($node->input_params ?? [], $context);

        $status = 'success';
        $errorMessage = null;
        $result = null;
        $nextConnection = null;

        if (!empty($node->condition_expression)) {
            // Node kondisi -> harus dievaluasi, hasilnya menentukan cabang mana yang diambil
            $missing = $this->findMissingVariables($node->condition_expression, $variables);

            if (!empty($missing)) {
                $status = 'failed';
                $errorMessage = 'Variabel tidak ditemukan: ' . implode(', ', $missing);
            } else {
                try {
                    $result = (bool) $this->expressionLanguage->evaluate(
                        $node->condition_expression,
                        $variables
                    );

                    $nextConnection = FlowConnection::where('source_node_id', $node->id)
                        ->where('branch_label', $result ? 'true' : 'false')
                        ->first();

                    if (!$nextConnection) {
                        $status = 'failed';
                        $errorMessage = "Tidak ada flow_connections dengan branch_label '"
                            . ($result ? 'true' : 'false') . "' dari node ini";
                    }
                } catch (SyntaxError $e) {
                    $status = 'failed';
                    $errorMessage = 'Sintaks condition_expression tidak valid: ' . $e->getMessage();
                } catch (\Throwable $e) {
                    $status = 'failed';
                    $errorMessage = 'Gagal mengevaluasi expression: ' . $e->getMessage();
                }
            }
        } else {
            // Node biasa (bukan kondisi) -> tidak ada percabangan, tinggal cari koneksi berikutnya
            $nextConnection = FlowConnection::where('source_node_id', $node->id)->first();
        }

        $durationMs = (int) ((microtime(true) - $start) * 1000);

        return NodeExecution::create([
            'simulation_id' => $simulationId,
            'flow_node_id' => $node->id,
            'node_label' => $node->label,
            'node_type' => $node->node_type,
            'status' => $status,
            'input_data' => $variables,
            'output_data' => [
                'result' => $result,
                'next_node_id' => $nextConnection?->target_node_id,
                'connection_id' => $nextConnection?->id,
            ],
            'message' => $status === 'success'
                ? ($result === null ? 'Node dieksekusi' : 'Hasil kondisi: ' . ($result ? 'true' : 'false'))
                : null,
            'duration_ms' => $durationMs,
            'executed_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    protected function findMissingVariables(string $expression, array $variables): array
    {
        preg_match_all('/\b[a-zA-Z_][a-zA-Z0-9_]*\b/', $expression, $matches);

        $usedNames = array_unique(array_diff($matches[0], $this->reservedWords));

        return array_values(array_diff($usedNames, array_keys($variables)));
    }
}