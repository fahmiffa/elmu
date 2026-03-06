<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StudentPresent extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['student_id', 'unit_schedules_id', 'teach_id', 'hal', 'Materi', 'Keterangan', 'head_id'];

    protected $appends = ['tanggal'];

    public function guru()
    {
        return $this->belongsTo(Teach::class, 'teach_id', 'id');
    }

    public function reg()
    {
        return $this->belongsTo(Head::class, 'head_id', 'id');
    }

    public function getTanggalAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->created_at)->translatedFormat('l, d F Y H:i');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedules_students::class, 'unit_schedules_id', 'unit_schedules_id')
            ->where('student_id', $this->student_id);
    }
}
