<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $appends = ['gambar'];
    protected $hidden  = ['created_at', 'updated_at','file'];

    public function getgambarAttribute()
    {
        return $this->file ? asset('storage/' . $this->file) : asset('image.png');
    }
}
