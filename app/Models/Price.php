<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    
    public function class ()
    {
        return $this->belongsTo(Kelas::class, 'kelas', 'id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'product', 'id');
    }

    public function item()
    {
        if($this->kelas)
        {
            return $this->belongsTo(Program::class, 'product', 'id');
        }
        else
        {
            return $this->belongsTo(Addon::class, 'product', 'id');
        }
    }
}
