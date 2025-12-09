<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $appends = ['tipe'];

    public function gettipeAttribute()
    {
        if ($this->status == 1) {
            if ($this->via == "cash") {
                return "Offline";
            } else {
                return "Online";
            }
        } else {
            return null;
        }

    }

    public function reg()
    {
        return $this->belongsTo(Head::class, 'head', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Price::class, 'price', 'product')->whereNull('kelas');
    }
}
