<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
    ];
    /**
     * Get the user associated with the school user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo - The user relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the school associated with the school user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo - The school relationship.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
