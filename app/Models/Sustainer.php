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
     * Obtiene las escuelas asociadas al sostenedor.
     */
    public function schools()
    {
        return $this->hasMany(School::class, 'sustainer_id');
    }
}
