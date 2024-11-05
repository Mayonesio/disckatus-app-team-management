<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',  // Asegurar que password está aquí
        'firebase_uid',
        'avatar',
        'avatar_color',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function playerProfile()
    {
        return $this->hasOne(PlayerProfile::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        return !!$role->intersect($this->roles)->count();
    }

    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    public function getHighestRole()
    {
        $roleHierarchy = [
            'super-admin' => 5,
            'captain' => 4,
            'sotg-captain' => 3,
            'treasurer' => 2,
            'player' => 1
        ];

        return $this->roles
            ->sortByDesc(fn($role) => $roleHierarchy[$role->slug] ?? 0)
            ->first();
    }

    public function isAdmin()
    {
        return $this->hasRole('super-admin');
    }

    public function isCaptain()
    {
        return $this->hasRole('captain');
    }
}