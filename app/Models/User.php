<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nombres',
        'apellidos',
        'email',
        'password',
        'status',
        'role',
        'created_by',
        'updated_by',
        'cancelled_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->nombres ?? '') . ' ' . ($this->apellidos ?? ''));
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctores::class, 'user_id'); 
    }
}