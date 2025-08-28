<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $guarded = [];

    public function unit()
    {
        return $this->belongsToMany(Unit::class, 'zone_units', 'zone_id', 'unit_id');
    }
}
