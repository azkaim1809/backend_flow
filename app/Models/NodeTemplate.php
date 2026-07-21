<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NodeTemplate extends Model
{
    protected $table = 'node_templates';

    protected $fillable = [
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
    ];

    protected $casts = [
        'default_input_params' => 'array',
        'default_output_template' => 'array',
    ];
}