<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function reg()
    {
        return $this->belongsTo(Head::class, 'head', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Price::class, 'price', 'product')->whereNull('kelas');
    }
}
