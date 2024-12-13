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
        'role_id',
        'school_id_session'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

  
    public function getUpdateAttributes(array $validated): array
    {
        $attributes = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        if (!!$validated['password']) {
            $attributes['password'] = bcrypt($validated['password']);
        }

        return $attributes;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_users')->withTimestamps();
    }

    public function historys()
    {
        return $this->hasMany(History::class);
    }

    public static function getUsersExcludingAuthenticated()
    {
        $authUserId = auth()->user()->id;

        return self::query()
            ->where('id', '!=', $authUserId)
            ->paginate(5);
    }

}
