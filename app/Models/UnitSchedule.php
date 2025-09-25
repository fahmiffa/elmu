<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitSchedule extends Model
{
    protected $appends = ['start_time', 'end_time'];

    public function getStartTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->start)->format('H:i');
    }

    public function getEndTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->end)->format('H:i');
    }
}
