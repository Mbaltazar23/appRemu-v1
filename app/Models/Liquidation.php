<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liquidation extends Model
{
    use HasFactory;

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'worker_id',
        'month',
        'year',
        'values',
        'details', // Añadimos 'details' para que se pueda asignar masivamente
        'glosa', // Añadimos glosa al $fillable si vas a asignarlo masivamente
    ];

    public static function getDistinctYears()
    {
        // Using 'distinct' and 'pluck' to get unique years
        return self::distinct('year')->pluck('year');
    }

    // Acceder a los detalles como un array
    public function getDetailsAttribute($value)
    {
        return json_decode($value, true); // Decodifica el JSON almacenado en 'details' a un array
    }

    // Modificar los detalles antes de guardarlos en el modelo
    public function setDetailsAttribute($value)
    {
        $this->attributes['details'] = json_encode($value); // Codifica el array en formato JSON antes de guardar
        /*
            "tuition_id": "ClassA",
            "title": "Sample Title",
            "value": "1000"
        */
    }
    /**
     * Verifica si existe una liquidación para un trabajador, mes y año específicos.
     *
     * @param int $month
     * @param int $year
     * @param int $workerId
     * @return bool
     */
    public static function exists($month, $year, $workerId)
    {
        // Usamos Eloquent para realizar la consulta
        $liquidacion = self::where('month', $month)
            ->where('year', $year)
            ->where('worker_id', $workerId)
            ->first(); // Devuelve el primer resultado o null si no existe

        // Si la liquidación existe, retornamos true, si no, false
        return $liquidacion ? true : false;
    }

    // Store a liquidation record
    public static function storeLiquidation($data)
    {
        return self::create($data);
    }

    // Update a liquidation record
    public static function updateLiquidation($id, $data)
    {
        $liquidation = self::find($id);
        if ($liquidation) {
            $liquidation->update($data);
        }
    }

    // Acceder a 'glosa' como texto (si es un BLOB binario)
    public function getGlosaAttribute($value)
    {
        return $value ? mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1') : null; // Si 'glosa' tiene datos binarios, los convierte a texto
    }

// Modificar 'glosa' antes de guardarlo como binario
    public function setGlosaAttribute($value)
    {
        $this->attributes['glosa'] = mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8'); // Si el valor es texto, lo convierte a binario
    }

    // Relación con el modelo Worker
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
