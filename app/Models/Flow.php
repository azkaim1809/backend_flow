<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    protected $fillable = [
        'name', 'description', 'version', 'status',
    ];

    public function nodes()
    {
        return $this->hasMany(FlowNode::class);
    }

    public function connections()
    {
        return $this->hasMany(FlowConnection::class);
    }

    public function simulations()
    {
        return $this->hasMany(Simulation::class);
    }
}