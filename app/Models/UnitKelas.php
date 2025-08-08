<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKelas extends Model
{
    public function kelasName()
    {
        return $this->belongsTo(kelas::class, 'kelas_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
