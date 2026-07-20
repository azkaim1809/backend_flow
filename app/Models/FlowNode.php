<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowNode extends Model
{
    protected $fillable = [
        'flow_id', 'template_id', 'label', 'node_type', 'icon', 'color',
        'pos_x', 'pos_y', 'input_params', 'validation_rules',
        'process_logic', 'output_template', 'condition_expression', 'order_index',
    ];

    protected $casts = [
        'input_params' => 'array',
        'output_template' => 'array',
        'pos_x' => 'float',
        'pos_y' => 'float',
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function template()
    {
        return $this->belongsTo(NodeTemplate::class, 'template_id');
    }

    public function outgoingConnections()
    {
        return $this->hasMany(FlowConnection::class, 'source_node_id');
    }

    public function incomingConnections()
    {
        return $this->hasMany(FlowConnection::class, 'target_node_id');
    }
}