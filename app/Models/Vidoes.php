<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vidoes extends Model
{
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function guru()
    {
        return $this->belongsTo(Teach::class, 'teach_id','id');
    }

    public function murid()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
