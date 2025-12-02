<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $hidden = ['created_at', 'updated_at', 'pile','user'];

    protected $appends = ['pdf'];

    public function getpdfAttribute()
    {
        return asset('storage/' . $this->pile);
    }

    public function users()
    {
        return $this->HasMany(User::class, 'id', 'user');
    }
}
