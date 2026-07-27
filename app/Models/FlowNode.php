<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowNode extends Model
{
    protected $fillable = [
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
        'flow_id',
    ];

    protected $casts = [
        'input_params' => 'array',
        'output_template' => 'array',
    ];

    public function connectionsFrom()
    {
        return $this->hasMany(FlowConnection::class, 'source_node_id');
    }

    public function connectionsTo()
    {
        return $this->hasMany(FlowConnection::class, 'target_node_id');
    }
}