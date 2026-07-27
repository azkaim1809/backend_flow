<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NodeExecution extends Model
{
    // tabel ini tidak punya kolom timestamps standar (pakai executed_at sendiri)
    public $timestamps = false;

    protected $fillable = [
        'simulation_id',
        'flow_node_id',
        'node_label',
        'node_type',
        'status',
        'input_data',
        'output_data',
        'message',
        'duration_ms',
        'executed_at',
        'error_message',
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'executed_at' => 'datetime',
    ];

    public function simulation()
    {
        return $this->belongsTo(Simulation::class);
    }

    public function flowNode()
    {
        return $this->belongsTo(FlowNode::class);
    }
}