<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $appends = ['age', 'absen','genders'];
    protected $hidden  = ['created_at', 'updated_at'];

    public function reg()
    {
        return $this->HasMany(Head::class, 'students', 'id');
    }

    public function getageAttribute()
    {
        return Carbon::parse($this->birth)->age . ' Tahun';
    }

    public function getgendersAttribute()
    {
        if($this->gender == 1)
        {
            return "Laki-laki";
        }

        if($this->gender == 2)
        {
            return "Perempuan";
        }

    }
    
    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }
    
    public function getabsenAttribute()
    {
        $today = Carbon::today();
        $hasEnteredToday = \DB::table('student_presents')
            ->where('student_id', $this->id)
            ->whereDate('created_at', $today)
            ->exists();

        return $hasEnteredToday ? 1 : 0;
        // return 0;
    }

    public function jadwal()
    {
        return $this->HasMany(Head::class, 'students', 'id');
    }

    public function present()
    {
        return $this->HasMany(StudentPresent::class, 'student_id', 'id');
    }
}
