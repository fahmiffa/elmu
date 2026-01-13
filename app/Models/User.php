<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{

    protected $appends = ['state', 'roles'];
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function getstateAttribute()
    {
        return $this->status == 0 ? 'TIdak Aktif' : 'Aktif';
    }

    public function getrolesAttribute()
    {
        if ($this->role == 0) {
            return "Admin";
        }

        if ($this->role == 3) {
            return "Guru";
        }

        if ($this->role == 2) {
            return "User";
        }

        if ($this->role == 4) {
            return "Operator";
        }
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function data()
    {
        if ($this->role == 3) {
            return $this->hasOne(Teach::class, 'user', 'id');
        } else {
            return $this->hasOne(Student::class, 'user', 'id');
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'zone_id',
        'status',
        'username',
        'nomor'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'deleted_at',
        'updated_at',
        'email_verified_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}
