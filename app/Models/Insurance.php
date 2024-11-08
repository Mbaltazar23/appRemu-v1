<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    const AFP = 1;
    const ISAPRE = 2;
    const FONASA = 3;

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
        self::FONASA => 'Fonasa',
    ];

    public function getTypeName()
    {
        return self::TYPES[$this->type] ?? 'Desconocido';
    }

    public static function obt_insurances($type)
    {
        if (!in_array($type, [self::AFP, self::ISAPRE, self::FONASA])) {
            return collect(); 
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

    public static function select_insurance_worker($id_insurance, $type, $id_school)
    {
        return Worker::where('insurance_' . $type, $id_insurance)
            ->where('school_id', $id_school)
            ->whereNull('termination_date')
            ->orderBy('last_name')
            ->orderBy('name')
            ->pluck('id');
    }
}
