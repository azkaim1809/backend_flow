<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flow;

class FlowController extends Controller
{
    //all flow
   public function index()
{
    $flows = Flow::select(
        'name',
        'description',
        'version',
        'status',
        'created_at',
        'updated_at'
    )->get();

    return response()->json([
        'message' => 'Data flow berhasil diambil',
        'data' => $flows
    ]);
}

    //menambahkan
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:100',
        'description' => 'nullable|string',
        'version' => 'required|string|max:20',
        'status' => 'required|string|max:20',
    ]);

    $flow = Flow::create([
        'name' => $request->name,
        'description' => $request->description,
        'version' => $request->version,
        'status' => $request->status,
    ]);

    return response()->json([
        'message' => 'Flow berhasil ditambahkan',
        'data' => [
            'name' => $flow->name,
            'description' => $flow->description,
            'version' => $flow->version,
            'status' => $flow->status,
            'created_at' => $flow->created_at,
            'updated_at' => $flow->updated_at,
        ]
    ], 201);
}

    // melihat berdasarkan ID
    public function show($id)
{
    $flow = Flow::find($id);

    if (!$flow) {
        return response()->json([
            'message' => 'Flow tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'message' => 'Data flow berhasil diambil',
        'data' => [
            'name' => $flow->name,
            'description' => $flow->description,
            'version' => $flow->version,
            'status' => $flow->status,
            'created_at' => $flow->created_at,
            'updated_at' => $flow->updated_at,
        ]
    ]);
}

    // update data berdasarkan ID
    public function update(Request $request, $id)
{
    $flow = Flow::find($id);

    if (!$flow) {
        return response()->json([
            'message' => 'Flow tidak ditemukan'
        ], 404);
    }

    $request->validate([
        'name' => 'required|string|max:100',
        'description' => 'nullable|string',
        'version' => 'required|string|max:20',
        'status' => 'required|string|max:20',
    ]);

    $flow->name = $request->name;
    $flow->description = $request->description;
    $flow->version = $request->version;
    $flow->status = $request->status;

    $flow->save();

    return response()->json([
        'message' => 'Flow berhasil diperbarui',
        'data' => [
            'name' => $flow->name,
            'description' => $flow->description,
            'version' => $flow->version,
            'status' => $flow->status,
            'created_at' => $flow->created_at,
            'updated_at' => $flow->updated_at,
        ]
    ]);
}

    // delet
    public function destroy($id)
{
    $flow = Flow::find($id);

    if (!$flow) {
        return response()->json([
            'message' => 'Flow tidak ditemukan'
        ], 404);
    }

    $flow->delete();

    return response()->json([
        'message' => 'Flow berhasil dihapus'
    ]);
}
}
