<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'permissions', // Almacena permisos en formato JSON
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public static function getAvailablePermissions()
    {
        return config('permissions');
    }

    public function updatePermissions(array $permissions)
    {
        $this->permissions = $permissions;
        $this->save();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
