<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    public function kelas_unit()
    {
        return $this->hasMany(UnitKelas::class, 'kelas_id', 'id');
    }
}
