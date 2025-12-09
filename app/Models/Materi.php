<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['pdf'];

    public function getpdfAttribute()
    {
        return asset('storage/' . $this->pile);
    }

    public function program()
    {
        return $this->HasOne(Program::class, 'id', 'program_id');
    }
}
