<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    const UPDATED_AT = null; // tidak ada kolom updated_at

    protected $fillable = [
        'flow_id', 'status', 'started_at', 'completed_at',
        'input_data', 'total_duration_ms',
    ];

    protected $casts = [
        'input_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function nodeExecutions()
    {
        return $this->hasMany(NodeExecution::class);
    }
}