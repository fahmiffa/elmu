<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogHead extends Model
{
    protected $fillable = ['head_id', 'student_id', 'tipe', 'keterangan'];

    public function head()
    {
        return $this->belongsTo(Head::class, 'head_id', 'id');
    }
}
