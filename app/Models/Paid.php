<?php
namespace App\Models;

use App\Models\Addon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Paid extends Model
{
    protected $appends = ['tempo', 'kit','total'];

    public function reg()
    {
        return $this->belongsTo(Head::class, 'head', 'id');
    }

    public function gettempoAttribute()
    {
        $date = Carbon::parse($this->created_at)
            ->addMonthNoOverflow()
            ->day(10)
            ->locale('id');
        return $formatted = $date->translatedFormat('l, d F Y');
    }

    public function getkitAttribute()
    {
        return $this->first == 1 ? Addon::select('id', 'name')->with(['price:harga,product'])->first() : null;
    }

    public function gettotalAttribute()
    {
        $price = (int) $this->reg->prices->harga;
        $kit   = (int) Addon::select('id', 'name')->with(['price:harga,product'])->first()->price->harga;
        return (int) $this->first == 1 ? $price + $kit : $price;
    }

    public function murid()
    {
        return $this->belongsTo(Student::class, 'head', 'students', 'id');
    }
}
