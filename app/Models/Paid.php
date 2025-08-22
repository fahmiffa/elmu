<?php
namespace App\Models;

use App\Models\Addon;
use Carbon\Carbon;
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
        return $this->first == 1 ? Addon::select('id', 'name')->with(['price:harga,product'])->first() : null;
        // return Addon::select('id', 'name')->with(['price:harga,product'])->first();
    }

    public function murid()
    {
        return $this->belongsTo(Student::class, 'head', 'students', 'id');
    }
}
