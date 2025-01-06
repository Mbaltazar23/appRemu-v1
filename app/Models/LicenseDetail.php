<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'day',
        'month',
        'year',
        'hours',
        'exists', // Este campo es para indicar si el día está o no dentro de las licencias
    ];

    /**
     * Inserta o actualiza horas de licencia en función del día, mes y año.
     */
    public static function insertOrUpdateHours($licenseId, $day, $month, $year, $hours)
    {
        return self::updateOrCreate(
            ['license_id' => $licenseId, 'day' => $day, 'month' => $month, 'year' => $year],
            ['hours' => $hours]
        );
    }

    /**
     * Inserta o actualiza los días de licencia.
     */
    public static function insertOrUpdateDays($licenseId, $day, $month, $year, $exists)
    {
        // Actualizamos o creamos un nuevo registro en la tabla hour_licenses con el license_id
        return self::updateOrCreate(
            ['license_id' => $licenseId, 'day' => $day, 'month' => $month, 'year' => $year],
            ['exists' => $exists]// Aquí actualizamos o insertamos el valor de 'exists'
        );
    }

    /**
     * Relación con el modelo License.
     */
    public function license()
    {
        return $this->belongsTo(License::class);
    }
}
