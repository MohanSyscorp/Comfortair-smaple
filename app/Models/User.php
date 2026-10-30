<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'password' => 'hashed',
            'permissions' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Determine if user has Super Admin authority.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Determine if account is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user has permission to access a specific module.
     * Allowed modules: 'products', 'gallery', 'enquiries', 'contact', 'users'
     */
    public function hasModuleAccess(string $module): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        // Admin role restrictions
        if ($this->role === 'admin') {
            if ($module === 'users') {
                return false;
            }
            return in_array($module, $this->permissions ?? ['products', 'gallery', 'enquiries', 'contact']);
        }

        // Content Manager restrictions
        if ($this->role === 'content_manager') {
            if (in_array($module, ['users', 'enquiries'])) {
                return false;
            }
            return in_array($module, $this->permissions ?? ['products', 'gallery', 'contact']);
        }

        return false;
    }
}
