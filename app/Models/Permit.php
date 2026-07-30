<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    protected $fillable = [
        'student_id',
        'schedule_student_id',   // sesi ASAL yang diganti
        'unit_schedules_id',     // sesi TUJUAN (ganti ke sesi ini)
        'why',
        'tanggal',               // tanggal ASAL sesi yang diganti
        'new_date',              // tanggal TUJUAN (ganti ke tanggal ini)
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function scheduleStudent()
    {
        return $this->belongsTo(Schedules_students::class, 'schedule_student_id', 'id');
    }

    public function unitSchedule()
    {
        return $this->belongsTo(UnitSchedule::class, 'unit_schedules_id', 'id');
    }

    /**
     * Relasi ke sesi jadwal TUJUAN (sesi yang diganti ke)
     * unit_schedules_id = sesi baru yang dituju
     */
    public function newUnitSchedule()
    {
        return $this->belongsTo(UnitSchedule::class, 'unit_schedules_id', 'id');
    }
}
