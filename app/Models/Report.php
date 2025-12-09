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
}
