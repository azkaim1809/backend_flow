<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    protected $table = 'simulations';

    public $timestamps = false;

    protected $fillable = [
        'flow_id',
        'status',
        'started_at',
        'completed_at',
        'input_data',
        'total_duration_ms',
        'created_at',
    ];

    protected $casts = [
        'input_data' => 'array',
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }
}