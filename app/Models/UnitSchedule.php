<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitSchedule extends Model
{
    protected $appends = ['start_time', 'end_time'];
    protected $hidden  = ['created_at', 'updated_at','deleted_at'];

    public function getStartTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->start)->format('H:i');
    }

    public function set()
    {
        return $this->belongsTo(Schedules_students::class, 'id', 'unit_schedules_id');
    }


    public function getEndTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->end)->format('H:i');
    }
}
