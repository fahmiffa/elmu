<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Raport extends Model
{
    public function murid()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user');
    }
}
