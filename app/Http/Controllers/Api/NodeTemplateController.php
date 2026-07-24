<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NodeTemplate;
use App\Traits\ApiResponse;

class NodeTemplateController extends Controller
{
    use ApiResponse;

    // Menampilkan semua data
    public function index()
    {
        $templates = NodeTemplate::select(
            'id',
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

        return $this->success(
            $templates,
            'Data template berhasil diambil'
        );
    }

    // Menambah data
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

        return $this->success([
            'id' => $template->id,
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
        ], 'Template berhasil ditambahkan', 201);
    }

    // Menampilkan data berdasarkan ID
    public function show($id)
    {
        $template = NodeTemplate::find($id);

        if (!$template) {
            return $this->notFound('Template tidak ditemukan');
        }

        return $this->success([
            'id' => $template->id,
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
        ], 'Detail template berhasil diambil');
    }

    // Update data
    public function update(Request $request, $id)
    {
        $template = NodeTemplate::find($id);

        if (!$template) {
            return $this->notFound('Template tidak ditemukan');
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

        return $this->success([
            'id' => $template->id,
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
        ], 'Template berhasil diperbarui');
    }

    // Hapus data
    public function destroy($id)
    {
        $template = NodeTemplate::find($id);

        if (!$template) {
            return $this->notFound('Template tidak ditemukan');
        }

        $template->delete();

        return $this->success(
            null,
            'Template berhasil dihapus'
        );
    }
}