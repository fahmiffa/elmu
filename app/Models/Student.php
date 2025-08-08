<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $appends = ['age', 'genders'];

    public function reg()
    {
        return $this->HasMany(Head::class, 'students', 'id');
    }

    public function getageAttribute()
    {
        return Carbon::parse($this->birth)->age . ' Tahun';
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }

    public function getgendersAttribute()
    {
        if ($this->gender == "1") {
            return "Laki-laki";
        }

        if ($this->gender == "2") {
            return "Perempuan";
        }
    }
}
