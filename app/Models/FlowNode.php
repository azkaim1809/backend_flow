<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowNode extends Model
{
    protected $table = 'flow_nodes';

    protected $fillable = [
        'flow_id',
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
    ];

    protected $casts = [
        'input_params' => 'array',
        'output_template' => 'array',
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function template()
    {
        return $this->belongsTo(NodeTemplate::class, 'template_id');
    }
}