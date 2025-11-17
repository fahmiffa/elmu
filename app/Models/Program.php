<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $hidden = ['created_at', 'updated_at','deleted_at'];
    public function price()
    {
        return $this->hasMany(Price::class, 'product', 'id');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'prices', 'product', 'kelas');
    }
}
