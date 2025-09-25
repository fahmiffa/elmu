<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StudentPresent extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['tanggal'];

    public function getTanggalAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->created_at)->translatedFormat('l, d F Y H:i');
    }
}
