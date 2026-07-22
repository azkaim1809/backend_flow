<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    // Tabel `simulations` cuma punya kolom `created_at`, gak ada `updated_at`.
    // Matikan auto-timestamp Eloquent, kita isi created_at manual di Controller.
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

    protected function casts(): array
    {
        return [
            'input_data'   => 'array',
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function nodeExecutions()
    {
        return $this->hasMany(NodeExecution::class);
    }
}