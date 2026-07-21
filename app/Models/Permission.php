<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false; // tidak ada created_at/updated_at

    protected $fillable = ['role_id', 'module_id', 'permission'];

    protected $casts = [
        'permission' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    
}