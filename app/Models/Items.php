<?php
namespace App\Models;

use App\Models\cart;
use App\Models\Stok;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    protected $appends = ['stoks'];

    public function getstoksAttribute()
    {
        $cart  = Cart::where('item', $this->id)->sum('count');
        $items = Stok::where('item', $this->id)->where('status', 1)->sum('count');
        return $items + $this->stok - $cart;
    }

    public function kategori()
    {
        return $this->belongsTo(Categori::class, 'cat', 'id');
    }

    public function size()
    {
        return $this->belongsTo(Unit::class, 'unit', 'id');
    }

}
