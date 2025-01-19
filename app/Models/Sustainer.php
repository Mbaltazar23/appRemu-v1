<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sustainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'rut',
        'business_name',
        'address',
        'commune',
        'region',
        'legal_nature',
        'legal_representative',
        'rut_legal_representative',
        'phone',
        'email',
    ];
   /**
     * Get the schools associated with the sustainer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany - The schools relationship.
     */
    public function schools()
    {
        return $this->hasMany(School::class);
    }
}
