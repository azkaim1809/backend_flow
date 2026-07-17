<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $table = 'permissions';

    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'module_id',
        'permission'
    ];

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