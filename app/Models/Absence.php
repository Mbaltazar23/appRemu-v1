<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    // Atributos que se pueden asignar masivamente
    protected $fillable = [
        'worker_id',      // Relación con el trabajador
        'day',            // Día de la ausencia
        'month',          // Mes de la ausencia
        'year',           // Año de la ausencia
        'reason',         // Motivo de la ausencia
        'minutes',        // Duración en minutos
        'with_consent',   // Si la ausencia tiene consentimiento
    ];



    /**
     * Obtener la fecha completa como un solo atributo.
     */
    public function getDateAttribute()
    {
        // Asegurarse de que los valores estén presentes y sean válidos
        if ($this->day && $this->month && $this->year) {
            return \Carbon\Carbon::create($this->year, $this->month, $this->day)->toDateString();
        }
        return null;
    }

    /**
     * Establecer la fecha a partir de un solo campo 'date'.
     */
    public function setDateAttribute($value)
    {
        // Si el valor de 'date' es válido, dividimos la fecha
        if ($value) {
            $date = \Carbon\Carbon::parse($value);  // Convierte la fecha a un objeto Carbon
            $this->attributes['day'] = $date->day;
            $this->attributes['month'] = $date->month;
            $this->attributes['year'] = $date->year;
        }
    }

    // Relación con Worker (Una ausencia pertenece a un trabajador)
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}