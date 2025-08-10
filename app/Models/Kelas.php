<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    public function kelas_unit()
    {
        return $this->hasMany(UnitKelas::class, 'kelas_id', 'id');
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'unit_kelas', 'kelas_id', 'unit_id');
    }
}
