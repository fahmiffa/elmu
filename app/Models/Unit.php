<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{

    protected $hidden  = ['created_at', 'updated_at'];
    protected $appends = ['kode', 'kelasn'];
    protected $guarded = [];

    public function getkodeAttribute()
    {
        $nom = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        return $nom;
    }

    public function getkelasnAttribute()
    {
        return $this->kelas->count();
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'unit_kelas', 'unit_id', 'kelas_id');
    }

    public function jadwal()
    {
        return $this->hasMany(UnitSchedule::class, 'unit_id', 'id');
    }
}
