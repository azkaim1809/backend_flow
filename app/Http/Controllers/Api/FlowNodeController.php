<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowNode;

class FlowNodeController extends Controller
{
    //ALL flownode
    public function index($flow)
{
    
    $nodes = FlowNode::where('flow_id', $flow)
        ->select(
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

    return response()->json([
        'message' => 'Data node berhasil diambil',
        'data' => $nodes
    ]);
}

    // menambahkan
    public function store(Request $request, $flow)
{
    $request->validate([
        'template_id' => 'required|exists:node_templates,id',
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

    $node = FlowNode::create([
        'flow_id' => $flow,
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

    return response()->json([
        'message' => 'Node berhasil ditambahkan',
        'data' => [
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
        ]
    ], 201);
}

    // menampilkan dengan ID
    public function show($id)
{
    $node = FlowNode::find($id);

    if (!$node) {
        return response()->json([
            'message' => 'Node tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Data node berhasil diambil',
        'data' => [
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
        ]
    ]);
}

    //update dengan ID
    public function update(Request $request, $id)
{
    $node = FlowNode::find($id);

    if (!$node) {
        return response()->json([
            'message' => 'Node tidak ditemukan'
        ], 404);
    }

    $request->validate([
        'template_id' => 'required|exists:node_templates,id',
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

    return response()->json([
        'message' => 'Node berhasil diperbarui',
        'data' => [
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
        ]
    ]);
}

    // delet
    public function destroy($id)
{
    $node = FlowNode::find($id);

    if (!$node) {
        return response()->json([
            'message' => 'Node tidak ditemukan'
        ], 404);
    }

    $node->delete();

    return response()->json([
        'message' => 'Node berhasil dihapus'
    ]);
}
}
