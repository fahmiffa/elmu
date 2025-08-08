<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{

    protected $appends = ['kode'];

    public function getkodeAttribute()
    {
        $nom = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        return $nom;
    }

    public function kelas()
    {
        return $this->hasMany(UnitKelas::class, 'unit_id', 'id');
    }
}
