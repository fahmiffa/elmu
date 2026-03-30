<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Raport extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function murid()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user');
    }

    public function reg()
    {
        return $this->belongsTo(Head::class, 'head_id', 'id');
    }
}
