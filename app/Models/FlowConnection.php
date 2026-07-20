<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowConnection extends Model
{
    const UPDATED_AT = null; // tabel ini tidak punya kolom updated_at

    protected $fillable = [
        'flow_id', 'source_node_id', 'target_node_id', 'branch_label',
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