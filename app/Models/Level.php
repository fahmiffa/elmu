<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    public function guru()
    {
        return $this->belongsTo(Teach::class, 'teach_user', 'user');
    }
}
