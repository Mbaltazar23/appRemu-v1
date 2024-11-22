<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'contract_type',
        'hire_date',
        'termination_date',
        'replacement_reason',
        'annexes', // Agregado para los anexos
    ];

    const CONTRACT_TYPES = [
        1 => 'Indefinido',
        2 => 'Plazo fijo',
        3 => 'Reemplazo',
        4 => 'Residual',
    ];

    const DURATION_OPTIONS = [
        'Indefinido' => 'Indefinido',
        'Plazo fijo' => 'Plazo fijo',
        'Reemplazo' => 'Reemplazo',
    ];

    const SCHEDULE_OPTIONS = [
        'Mañana' => 'Mañana',
        'Tarde' => 'Tarde',
        'Nocturna' => 'Nocturna',
    ];

    const LEVELS_OPTIONS = [
        'Básica' => 'Básica',
        'Media' => 'Media',
        'Superior' => 'Superior',
    ];

    // Accesor para obtener los anexos como un arreglo
    public function getAnnexesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    // Mutador para guardar los anexos como JSON
    public function setAnnexesAttribute($value)
    {
        $this->attributes['annexes'] = json_encode($value);
    }

    // Arreglo para los tipos de contrato
    public static function getContractTypes()
    {
        return self::CONTRACT_TYPES;
    }

    // Mostrar el valor del tipo de contrato
    public function getContractTypesDescription()
    {
        return self::CONTRACT_TYPES[$this->function_worker] ?? 'Desconocido';
    }
    /**
     * Verifica si existe un contrato para un trabajador
     */
    public static function contractExists($idWorker)
    {
        return self::where('worker_id', $idWorker)->exists();
    }

    /**
     * Obtiene un contrato de trabajo
     */
    public static function getContract($idWorker)
    {
        return self::where('worker_id', $idWorker)->first();
    }

    public static function createOrUpdateContract($workerId, Request $request)
    {
        $data = $request->only(['contract_type', 'hire_date', 'termination_date', 'replacement_reason']);
        return self::updateOrCreate(['worker_id' => $workerId], $data);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}
