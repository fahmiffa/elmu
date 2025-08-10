<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{

    protected $appends = ['kode'];
    protected $guarded = [];

    public function getkodeAttribute()
    {
        $nom = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        return $nom;
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'unit_kelas', 'unit_id', 'kelas_id');
    }
}
