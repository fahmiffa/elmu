<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    protected $hidden = ['created_at', 'updated_at'];
    
    public function guru()
    {
        return $this->belongsTo(Teach::class, 'teach_id', 'id');
    }

    public function meet()
    {
        return $this->hasMany(Schedule_meet::class, 'schedule_id', 'id');
    }

    public function programs()
    {
        return $this->belongsTo(Program::class, 'program', 'id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }

    public function class ()
    {
        return $this->belongsTo(Kelas::class, 'kelas', 'id');
    }


    public function siswa()
    {
        return $this->hasMany(Schedules_students::class, 'schedule_id', 'id');
    }


    public function waktu()
    {
        return $this->belongsToMany(Schedule_date::class, 'schedule_meets', 'schedule_id', 'id');
    }

    public function murid()
    {
        return $this->belongsToMany(Student::class, 'schedules_students', 'schedule_id', 'student_id');
    }

}
