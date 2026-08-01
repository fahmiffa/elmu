<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
     protected $hidden = ['created_at', 'updated_at'];
     
    public function users()
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'user','user');
    }

    public function teach()
    {
        return $this->belongsTo(Teach::class, 'user','user');
    }
}
