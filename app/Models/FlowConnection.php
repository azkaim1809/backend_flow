<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowConnection extends Model
{
    protected $table = 'flow_connections';

    public $timestamps = false;

    protected $fillable = [
        'flow_id',
        'source_node_id',
        'target_node_id',
        'branch_label',
        'created_at',
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function sourceNode()
    {
        return $this->belongsTo(FlowNode::class, 'source_node_id');
    }

    public function targetNode()
    {
        return $this->belongsTo(FlowNode::class, 'target_node_id');
    }
}