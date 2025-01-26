<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    use HasFactory;

    protected $fillable = [
        'name',
        'permissions', // Stores permissions in JSON format
    ];
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Get the available permissions from the configuration.
     *
     * @return array - List of available permissions.
     */
    public static function getAvailablePermissions() {
        return config('permissions');
    }

    /**
     * Update the permissions for the role.
     *
     * @param array $permissions - List of permissions to update.
     * @return void
     */
    public function updatePermissions(array $permissions) {
        $this->permissions = $permissions;
        $this->save();
    }

    /**
     * Get the users associated with this role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The relationship with the User model.
     */
    public function users() {
        return $this->hasMany(User::class);
    }

}
