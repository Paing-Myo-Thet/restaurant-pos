<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Permission;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(Permission $permission): bool
    {
        return (bool) $this->role?->allows($permission);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->role?->is_system;
    }

    /**
     * Where to send the user when they open the dashboard without access to it.
     */
    public function homeRoute(): string
    {
        return match (true) {
            $this->hasPermission(Permission::DashboardView) => 'dashboard',
            $this->hasPermission(Permission::KitchenManage) => 'kitchen.index',
            $this->hasPermission(Permission::OrdersView) => 'sales.index',
            default => 'profile.edit',
        };
    }
}
