<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    const AFP = 1;
    const ISAPRE = 2;
    const FONASA = 3; // Nueva constante para Fonasa

    protected $fillable = [
        'name',
        'type',
        'cotizacion',
        'rut',
    ];

    /**
     * Tipos de insurance.
     *
     * @var array<int, string>
     */
    const TYPES = [
        self::AFP => 'AFP',
        self::ISAPRE => 'Salud',
        self::FONASA => 'Fonasa', // Agregando Fonasa a los tipos
    ];

    public function getTypeName()
    {
        return self::TYPES[$this->type] ?? 'Desconocido';
    }

    public static function obt_insurances($type)
    {
        // Comprobar si el tipo es válido
        if (!in_array($type, [self::AFP, self::ISAPRE, self::FONASA])) {
            return collect(); // Retorna una colección vacía
        }

        return self::where('type', $type)->get();
    }

    public static function getInsuranceTypes()
    {
        return collect(self::TYPES);
    }

    public static function obt_nombre_insurance($id)
    {
        return self::where('id', $id)->value('name');
    }

    public static function obt_cotizacion_insurance($id)
    {
        return self::where('id', $id)->value('cotizacion');
    }

    public static function obt_datos_insurance($id)
    {
        return self::find($id);
    }

    public static function select_insurance_worker($id_insurance, $id_school)
    {
        return Worker::where('id_insurance', $id_insurance)
            ->where('id_school', $id_school)
            ->whereNull('termination_date')
            ->orderBy('last_name')
            ->orderBy('name')
            ->pluck('id');
    }
}
