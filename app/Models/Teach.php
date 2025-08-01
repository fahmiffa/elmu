<?php
namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Teach extends Model
{
    protected $appends = ['age'];

    public function getageAttribute()
    {
        return Carbon::parse($this->birth)->age.' Tahun';
    }
}
