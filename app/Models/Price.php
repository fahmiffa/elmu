<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    public function class ()
    {
        return $this->belongsTo(Kelas::class, 'kelas', 'id');
    }

     public function program()
    {
        return $this->belongsTo(Program::class, 'product', 'id');
    }
}
