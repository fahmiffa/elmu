<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = ['teach_id', 'nominal', 'status', 'tanggal', 'sesi', 'persentase', 'total', 'jumlah_pertemuan'];
    protected $hidden = ['created_at', 'updated_at'];

    public function teach()
    {
        return $this->belongsTo(Teach::class, 'teach_id', 'id');
    }

    public function head()
    {
        return $this->belongsTo(Head::class, 'head_id', 'id');
    }
}
