<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    public function price()
    {
        return $this->belongsTo(Price::class, 'id', 'product')->whereNull('kelas');
    }
}
