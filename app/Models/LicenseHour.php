<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseHour extends Model {

    use HasFactory;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'license_id',
        'day',
        'month',
        'year',
        'hours',
    ];

    /**
     * Define the relationship with the License model.
     * 
     * A LicenseHour belongs to a License, which is referenced by 'license_id'.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function license() {
        return $this->belongsTo(License::class);
    }

}
