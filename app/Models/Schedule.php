<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    public function guru()
    {
        return $this->belongsTo(Teach::class, 'teach_id', 'id');
    }


    public function meet()
    {
        return $this->hasMany(Schedule_meet::class, 'schedule_id', 'id');
    }

    public function reg()
    {
        return $this->HasOne(Head::class, 'id', 'head');
    }

    public function waktu()
    {
        return $this->belongsToMany(Schedule_date::class, 'schedule_meets', 'schedule_id','id');
    }
}
