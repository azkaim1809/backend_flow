<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowConnection extends Model
{
    // tabel ini cuma punya created_at, tidak ada updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'source_node_id',
        'target_node_id',
        'branch_label',
        'flow_id',
    ];

    public function sourceNode()
    {
        return $this->belongsTo(FlowNode::class, 'source_node_id');
    }

    public function targetNode()
    {
        return $this->belongsTo(FlowNode::class, 'target_node_id');
    }
}