<?php
namespace App\Models;

use App\Models\Head;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Head extends Model
{
    protected $table   = 'head';
    protected $appends = ['kode', 'waktu', 'induk'];

    public function murid()
    {
        return $this->belongsTo(Student::class, 'students', 'id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Schedule::class, 'id', 'head');
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

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }

    public function bill()
    {
        return $this->hasMany(Paid::class, 'head', 'id');
    }

    public function getkodeAttribute()
    {
        $nom = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        return 'P' . $nom;
    }

    public function getwaktuAttribute()
    {
        $date             = Carbon::parse($this->created_at)->locale('id');
        return $formatted = $date->translatedFormat('l, d F Y H:i');

    }

    public function getindukAttribute()
    {
        return str_pad($this->number, 4, '0', STR_PAD_LEFT);
    }

}
