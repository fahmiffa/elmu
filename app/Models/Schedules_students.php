<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedules_students extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    
    public function jadwal()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id');
    }
}
