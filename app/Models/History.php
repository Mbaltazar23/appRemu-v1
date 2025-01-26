<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model {

    use HasFactory;

    // Define the fillable attributes to allow mass assignment
    protected $fillable = ['user_id', 'action'];

    /**
     * Relationship with the User model.
     * 
     * This method defines a relationship where each history record
     * belongs to a specific user. It indicates that the `user_id`
     * column in the `history` table references the `id` column in the
     * `users` table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        // This indicates that each history record belongs to a user
        return $this->belongsTo(User::class, 'user_id');
    }

}
