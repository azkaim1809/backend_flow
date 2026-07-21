<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NodeTemplate;


class NodeTemplateController extends Controller
{
    //menampilkan semua data
    public function index()
{
    $templates = NodeTemplate::select(
        'name',
        'node_type',
        'description',
        'icon',
        'color',
        'default_input_params',
        'default_validation',
        'default_process_logic',
        'default_output_template',
        'used_count',
        'created_at',
        'updated_at'
    )->get();

    return response()->json([
        'message' => 'Data template berhasil diambil',
        'data' => $templates
    ]);
}

    //menambah data
   public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'node_type' => 'required|string|max:50',
        'description' => 'nullable|string',
        'icon' => 'nullable|string|max:50',
        'color' => 'nullable|string|max:20',
        'default_input_params' => 'nullable|array',
        'default_validation' => 'nullable|string',
        'default_process_logic' => 'nullable|string',
        'default_output_template' => 'nullable|array',
        'used_count' => 'nullable|integer',
    ]);

    $template = NodeTemplate::create([
        'name' => $request->name,
        'node_type' => $request->node_type,
        'description' => $request->description,
        'icon' => $request->icon,
        'color' => $request->color,
        'default_input_params' => $request->default_input_params,
        'default_validation' => $request->default_validation,
        'default_process_logic' => $request->default_process_logic,
        'default_output_template' => $request->default_output_template,
        'used_count' => $request->used_count ?? 0,
    ]);

    return response()->json([
        'message' => 'Template berhasil ditambahkan',
        'data' => [
            'name' => $template->name,
            'node_type' => $template->node_type,
            'description' => $template->description,
            'icon' => $template->icon,
            'color' => $template->color,
            'default_input_params' => $template->default_input_params,
            'default_validation' => $template->default_validation,
            'default_process_logic' => $template->default_process_logic,
            'default_output_template' => $template->default_output_template,
            'used_count' => $template->used_count,
            'created_at' => $template->created_at,
            'updated_at' => $template->updated_at,
        ]
    ],201);
}

    //menampilkan dat adengan ID
    public function show($id)
{
    $template = NodeTemplate::find($id);

    if (!$template) {
        return response()->json([
            'message' => 'Template tidak ditemukan'
        ],404);
    }

    return response()->json([
        'message' => 'Detail template berhasil diambil',
        'data' => $template
    ]);
}

    //Update data
    public function update(Request $request, $id)
{
    $template = NodeTemplate::find($id);

    if (!$template) {
        return response()->json([
            'message' => 'Template tidak ditemukan'
        ],404);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'node_type' => 'required|string|max:50',
        'description' => 'nullable|string',
        'icon' => 'nullable|string|max:50',
        'color' => 'nullable|string|max:20',
        'default_input_params' => 'nullable|array',
        'default_validation' => 'nullable|string',
        'default_process_logic' => 'nullable|string',
        'default_output_template' => 'nullable|array',
        'used_count' => 'nullable|integer',
    ]);

    $template->update($request->all());

    return response()->json([
        'message' => 'Template berhasil diperbarui',
        'data' => $template
    ]);
}

    //delet
    public function destroy($id)
{
    $template = NodeTemplate::find($id);

    if (!$template) {
        return response()->json([
            'message' => 'Template tidak ditemukan'
        ],404);
    }

    $template->delete();

    return response()->json([
        'message' => 'Template berhasil dihapus'
    ]);
}
}
