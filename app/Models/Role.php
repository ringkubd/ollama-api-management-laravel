<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Check if the role has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions !== null && in_array($permission, $this->permissions);
    }

    /**
     * Give a permission to the role.
     *
     * @param string $permission
     * @return $this
     */
    public function givePermission(string $permission): self
    {
        $permissions = $this->permissions ?? [];

        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
        }

        return $this;
    }

    /**
     * Remove a permission from the role.
     *
     * @param string $permission
     * @return $this
     */
    public function revokePermission(string $permission): self
    {
        if ($this->permissions !== null) {
            $this->permissions = array_values(array_diff($this->permissions, [$permission]));
        }

        return $this;
    }
}
