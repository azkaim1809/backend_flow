<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;

class Module extends Model
{
    public $timestamps = false; // tabel ini tidak punya created_at/updated_at

    protected $fillable = ['name', 'slug', 'url', 'sort_order'];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
