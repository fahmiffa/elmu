<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Paid extends Model
{
    protected $appends = ['tempo'];

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
}
