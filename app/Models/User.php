<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {

    use HasApiTokens,
        HasFactory,
        Notifiable;

    // Fields that can be mass-assigned
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'school_id_session'
    ];
    // Fields that should be hidden from serialization
    protected $hidden = [
        'password',
        'remember_token',
    ];
    // Cast the 'email_verified_at' attribute to a datetime object
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Prepare the attributes for updating the user.
     * If a password is provided, it will be hashed.
     */
    public function getUpdateAttributes(array $validated): array {
        $attributes = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        // If password is provided, hash it and add to the attributes array
        if (!!$validated['password']) {
            $attributes['password'] = Hash::make($validated['password']);
        }

        return $attributes;
    }

    /**
     * Get a list of users excluding the currently authenticated user.
     * It paginates the results to show 5 users at a time.
     */
    public static function getUsersExcludingAuthenticated() {
        $authUserId = auth()->user()->id;

        return self::query()
                        ->where('id', '!=', $authUserId) // Exclude the authenticated user
                        ->paginate(5); // Paginate results with 5 users per page
    }

    /**
     * Relationship: A User belongs to a Role.
     */
    public function role() {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relationship: A User can belong to many Schools through a pivot table.
     */
    public function schools() {
        return $this->belongsToMany(School::class, 'school_users')->withTimestamps();
    }

    /**
     * Relationship: A User has many History records.
     */
    public function historys() {
        return $this->hasMany(History::class);
    }

}
