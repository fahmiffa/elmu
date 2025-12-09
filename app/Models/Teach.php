<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Teach extends Model
{
    protected $appends = ['age'];
    protected $hidden  = ['created_at', 'updated_at'];

    public function murid()
    {
        return $this->HasMany(Head::class, 'unit', 'unit_id');
    }

    public function head()
    {
        return $this->HasMany(Head::class, 'unit', 'unit_id');
    }

    public function getageAttribute()
    {
        return Carbon::parse($this->birth)->age . ' Tahun';
    }

    public function akun()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
