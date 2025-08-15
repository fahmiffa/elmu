<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule_meet extends Model
{
    public function waktu()
    {
        return $this->hasMany(Schedule_date::class, 'schedule_meet_id', 'id');
    }
}
