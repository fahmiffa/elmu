<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedules_students extends Model
{
    protected $fillable = ['head', 'student_id', 'unit_schedules_id', 'program_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function reg()
    {
        return $this->belongsTo(Head::class, 'head', 'id');
    }

    public function sch()
    {
        return $this->hasMany(UnitSchedule::class, 'id', 'unit_schedules_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }
}
