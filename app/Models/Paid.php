<?php
namespace App\Models;

use Carbon\Carbon;
use App\Models\Addon;
use Illuminate\Database\Eloquent\Model;

class Paid extends Model
{
    protected $appends = ['tempo', 'kit'];

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
        return $this->first == 1 ? Addon::select('id','name')->with(['price:harga,product'])->first() : null;
    }
}
