<?php
namespace App\Models;

use App\Models\Head;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Head extends Model
{
    use SoftDeletes;
    protected $table   = 'head';
    protected $appends = ['kode', 'waktu', 'induk'];

    public function murid()
    {
        return $this->belongsTo(Student::class, 'students', 'id');
    }

    public function present()
    {
        return $this->hasMany(StudentPresent::class, 'student_id', 'students');
    }

    public function level()
    {
        return $this->hasMany(Level::class, 'head', 'id');
    }

    public function paket()
    {
        return $this->belongsTo(Program::class, 'program', 'id');
    }

    public function kontrak()
    {
        return $this->belongsTo(Payment::class, 'payment', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Price::class, 'price', 'id');
    }

    public function prices()
    {
        return $this->belongsTo(Price::class, 'price', 'id');
    }

    public function programs()
    {
        return $this->belongsTo(Program::class, 'program', 'id');
    }

    public function class ()
    {
        return $this->belongsTo(Kelas::class, 'kelas', 'id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }

    public function bill()
    {
        return $this->hasMany(Paid::class, 'head', 'id');
    }

    public function lay()
    {
        return $this->hasMany(Order::class, 'head', 'id');
    }

    public function getkodeAttribute()
    {
        $nom = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        return 'P' . $nom;
    }

    public function getwaktuAttribute()
    {
        $date             = Carbon::parse($this->crated_at)->locale('id');
        return $formatted = $date->translatedFormat('l, d F Y');

    }

    public function getindukAttribute()
    {
        $munit  = str_pad($this->number, 3, '0', STR_PAD_LEFT);
        $global = str_pad($this->global, 4, '0', STR_PAD_LEFT);
        $unit   = str_pad($this->units->id, 3, '0', STR_PAD_LEFT);
        return $global . '' . $unit . '' . $munit . '/' . $this->programs->kode;
    }

    public function jadwal()
    {
        return $this->belongsToMany(UnitSchedule::class, 'schedules_students', 'head', 'unit_schedules_id');
    }

}
