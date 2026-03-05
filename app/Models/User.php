<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'name',
        'is_active',
        'deleted_at',
        'updated_by',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Tell Laravel to use password_hash for authentication
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Roles relationship
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_roles',
            'user_id',
            'role_id'
        );
    }

    // public function hasRole($role): bool
    // {
    //     return $this->roles->contains('name', $role);
    // }

    // public function hasAnyRole(array $roles): bool
    // {
    //     return in_array($this->role, $roles);
    // }


    public function hasRole($role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }


    public function getRoleNameAttribute()
    {
        return $this->roles->pluck('name')->join(', ');
    }


}
