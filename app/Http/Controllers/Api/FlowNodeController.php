<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowNode;
use App\Models\NodeTemplate;
use App\Traits\ApiResponse;
use App\Models\Flow;

class FlowNodeController extends Controller
{
    use ApiResponse;

    // Semua node berdasarkan flow
    public function index($flow)
    {
        $nodes = FlowNode::where('flow_id', $flow)
            ->select(
                'id',
                'flow_id',
                'template_id',
                'label',
                'node_type',
                'icon',
                'color',
                'pos_x',
                'pos_y',
                'input_params',
                'validation_rules',
                'process_logic',
                'output_template',
                'condition_expression',
                'order_index',
                'created_at',
                'updated_at'
            )
            ->get();

        return $this->success(
            $nodes,
            'Data node berhasil diambil'
        );
    }

    // Menambahkan node
public function store(Request $request, $flow)
{
    // Cek apakah Flow ada
    $flowData = Flow::find($flow);

    if (!$flowData) {
        return $this->notFound('Flow tidak ditemukan');
    }

    $request->validate([
        'template_id' => 'nullable|exists:node_templates,id',
        // label & node_type wajib diisi HANYA kalau template_id tidak dikirim
        'label' => 'required_without:template_id|string|max:255',
        'node_type' => 'required_without:template_id|string|max:50',
        'icon' => 'nullable|string|max:50',
        'color' => 'nullable|string|max:20',
        'pos_x' => 'nullable|numeric',
        'pos_y' => 'nullable|numeric',
        'input_params' => 'nullable|array',
        'validation_rules' => 'nullable|string',
        'process_logic' => 'nullable|string',
        'output_template' => 'nullable|array',
        'condition_expression' => 'nullable|string',
        'order_index' => 'required|integer',
    ]);

    // Ambil template kalau template_id dikirim
    $template = null;
    if ($request->template_id) {
        $template = NodeTemplate::find($request->template_id);
    }

    $node = FlowNode::create([
        'flow_id' => $flow,
        'template_id' => $request->template_id,
        // kalau field dikirim, pakai itu. Kalau tidak, ambil dari template. Kalau template juga tidak ada, fallback default.
        'label' => $request->label ?? $template?->name ?? 'Untitled Node',
        'node_type' => $request->node_type ?? $template?->node_type,
        'icon' => $request->icon ?? $template?->icon,
        'color' => $request->color ?? $template?->color,
        'pos_x' => $request->pos_x ?? 0,
        'pos_y' => $request->pos_y ?? 0,
        'input_params' => $request->input_params ?? $template?->default_input_params,
        'validation_rules' => $request->validation_rules ?? $template?->default_validation,
        'process_logic' => $request->process_logic ?? $template?->default_process_logic,
        'output_template' => $request->output_template ?? $template?->default_output_template,
        'condition_expression' => $request->condition_expression, // ini tidak ada di node_templates, jadi tetap dari request saja
        'order_index' => $request->order_index,
    ]);

    // opsional: catat berapa kali template ini dipakai
    $template?->increment('used_count');

    return $this->success([
        'id' => $node->id,
        'flow_id' => $node->flow_id,
        'template_id' => $node->template_id,
        'label' => $node->label,
        'node_type' => $node->node_type,
        'icon' => $node->icon,
        'color' => $node->color,
        'pos_x' => $node->pos_x,
        'pos_y' => $node->pos_y,
        'input_params' => $node->input_params,
        'validation_rules' => $node->validation_rules,
        'process_logic' => $node->process_logic,
        'output_template' => $node->output_template,
        'condition_expression' => $node->condition_expression,
        'order_index' => $node->order_index,
        'created_at' => $node->created_at,
        'updated_at' => $node->updated_at,
    ], 'Node berhasil ditambahkan', 201);
}
    // Menampilkan node berdasarkan ID
    public function show($id)
    {
        $node = FlowNode::find($id);

        if (!$node) {
            return $this->notFound('Node tidak ditemukan');
        }

        return $this->success([
            'id' => $node->id,
            'flow_id' => $node->flow_id,
            'template_id' => $node->template_id,
            'label' => $node->label,
            'node_type' => $node->node_type,
            'icon' => $node->icon,
            'color' => $node->color,
            'pos_x' => $node->pos_x,
            'pos_y' => $node->pos_y,
            'input_params' => $node->input_params,
            'validation_rules' => $node->validation_rules,
            'process_logic' => $node->process_logic,
            'output_template' => $node->output_template,
            'condition_expression' => $node->condition_expression,
            'order_index' => $node->order_index,
            'created_at' => $node->created_at,
            'updated_at' => $node->updated_at,
        ], 'Data node berhasil diambil');
    }

    // Update node
    public function update(Request $request, $id)
    {
        $node = FlowNode::find($id);

        if (!$node) {
            return $this->notFound('Node tidak ditemukan');
        }

        $request->validate([
            'template_id' => 'nullable|exists:node_templates,id',
            'label' => 'required|string|max:255',
            'node_type' => 'required|string|max:50',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'pos_x' => 'required|numeric',
            'pos_y' => 'required|numeric',
            'input_params' => 'nullable|array',
            'validation_rules' => 'nullable|string',
            'process_logic' => 'nullable|string',
            'output_template' => 'nullable|array',
            'condition_expression' => 'nullable|string',
            'order_index' => 'required|integer',
        ]);

        $node->update([
            'template_id' => $request->template_id,
            'label' => $request->label,
            'node_type' => $request->node_type,
            'icon' => $request->icon,
            'color' => $request->color,
            'pos_x' => $request->pos_x,
            'pos_y' => $request->pos_y,
            'input_params' => $request->input_params,
            'validation_rules' => $request->validation_rules,
            'process_logic' => $request->process_logic,
            'output_template' => $request->output_template,
            'condition_expression' => $request->condition_expression,
            'order_index' => $request->order_index,
        ]);

        return $this->success([
            'id' => $node->id,
            'flow_id' => $node->flow_id,
            'template_id' => $node->template_id,
            'label' => $node->label,
            'node_type' => $node->node_type,
            'icon' => $node->icon,
            'color' => $node->color,
            'pos_x' => $node->pos_x,
            'pos_y' => $node->pos_y,
            'input_params' => $node->input_params,
            'validation_rules' => $node->validation_rules,
            'process_logic' => $node->process_logic,
            'output_template' => $node->output_template,
            'condition_expression' => $node->condition_expression,
            'order_index' => $node->order_index,
            'created_at' => $node->created_at,
            'updated_at' => $node->updated_at,
        ], 'Node berhasil diperbarui');
    }

    // Hapus node
    public function destroy($id)
    {
        $node = FlowNode::find($id);

        if (!$node) {
            return $this->notFound('Node tidak ditemukan');
        }

        $node->delete();

        return $this->success(
            null,
            'Node berhasil dihapus'
        );
    }
}