<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StudentPresent extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['student_id', 'unit_schedules_id', 'teach_id', 'hal', 'Materi', 'Keterangan'];

    protected $appends = ['tanggal'];

    public function guru()
    {
        return $this->belongsTo(Teach::class, 'teach_id', 'id');
    }

    public function getTanggalAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->created_at)->translatedFormat('l, d F Y H:i');
    }
}
