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
        'details',
        'annex_name',
        'annex_description',
        'replacement_reason', // Agregado para motivo de reemplazo
    ];

    const CONTRACT_TYPES = [
        1 => 'Indefinido',
        2 => 'Plazo fijo',
        3 => 'Reemplazo',
        4 => 'Residual',
    ];

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

    /**
     * Obtiene los anexos de contrato de un trabajador
     */
    public static function getAnnexes($idWorker)
    {
        return self::where('worker_id', $idWorker)
            ->select('annex_name', 'annex_description')
            ->get();
    }

    public static function createOrUpdateContract($workerId, Request $request)
    {
        $data = $request->only(['contract_type', 'hire_date', 'termination_date', 'replacement_reason']);
        return self::updateOrCreate(['worker_id' => $workerId], $data);
    }

    /**
     * Ver un anexo específico
     */
    public static function getAnnex($idAnexo)
    {
        return self::find($idAnexo)->annex_description ?? null;
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}
