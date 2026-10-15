<?php

namespace App\Models;

use App\Enums\Permission;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'permissions',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_system' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * The system role (Admin) can do everything, including anything added later,
     * and can't be edited or deleted, so there is always a way back in.
     */
    public function allows(Permission $permission): bool
    {
        return $this->is_system || in_array($permission->value, $this->permissions ?? [], true);
    }
}
