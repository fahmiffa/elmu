<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Raport extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    public function murid()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user');
    }
}
