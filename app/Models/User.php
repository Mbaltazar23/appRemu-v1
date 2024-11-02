<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'school_id_session',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array', // Cast a array
    ];

    protected $appends = ['roleName'];

    const SUPER_ADMIN = 1;
    const ADMIN = 2;
    const CONTADOR = 3;
    const SOSTENEDOR = 4;
    const USUARIO = 5;

    const ROLES = [
        1 => 'Super Admin', // Nuevo rol añadido
        2 => 'Administrador',
        3 => 'Contador',
        4 => 'Sostenedor',
        5 => 'Usuario',
    ];

    public function getUpdateAttributes(array $validated): array
    {
        $attributes = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!!$validated['password']) {
            $attributes['password'] = bcrypt($validated['password']);
        }

        return $attributes;
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_users')->withTimestamps();
    }

    public function isSuperAdmin()
    {
        return $this->role === static::SUPER_ADMIN;
    }

    public function isAdmin()
    {
        return $this->role === static::ADMIN;
    }

    public function isContador()
    {
        return $this->role === static::CONTADOR;
    }

    public function isSostenedor()
    {
        return $this->role === static::SOSTENEDOR;
    }

    public function isUsuario()
    {
        return $this->role === static::USUARIO;
    }

    public function getRoleNameAttribute()
    {
        return static::ROLES[$this->role] ?? 'None';
    }

    public static function getRolesAccordingToUserRole($role = null)
    {
        $role = $role ?: auth()->user()->role;
        return collect(static::ROLES)
            ->when($role !== static::SUPER_ADMIN, function ($collection) {
                return $collection->forget(static::SUPER_ADMIN);
            });
    }

    public static function getUsersExcludingAuthenticated()
    {
        $authUserId = auth()->user()->id;

        return self::query()
            ->where('id', '!=', $authUserId)
            ->paginate(5);
    }

    public static function resetSchoolIdSession()
    {
        $users = self::all();
        foreach ($users as $user) {
            $user->update(['school_id_session' => null]);
        }
    }

    public function getSchoolsForContador()
    {
        if ($this->isContador()) {
            return $this->schools;
        }
        return collect();
    }

    public function updatePermissions(array $permissions)
    {
        $this->permissions = $permissions;
        $this->save();
    }

    public static function getPermissions()
    {
        return config('permissions');
    }

}
