<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $appends = ['gambar'];

    public function getgambarAttribute()
    {
        return asset('storage/' . $this->file);
    }
}
