<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HourLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'hours',
    ];

    /**
     * Relación con el modelo License.
     */
    public function license()
    {
        return $this->belongsTo(License::class, 'license_id');
    }
}
