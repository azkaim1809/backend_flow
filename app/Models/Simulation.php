<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    // tabel ini cuma punya created_at, tidak ada updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'status',
        'started_at',
        'completed_at',
        'input_data',
        'total_duration_ms',
        'flow_id',
    ];

    protected $casts = [
        'input_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

     public function nodeExecutions()
    {
        return $this->hasMany(NodeExecution::class);
    }


    public function executions()
    {
        return $this->hasMany(NodeExecution::class);
    }
}