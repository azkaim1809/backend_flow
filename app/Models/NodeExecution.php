<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NodeExecution extends Model
{
    protected $table = 'node_executions';

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