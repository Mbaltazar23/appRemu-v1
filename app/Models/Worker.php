<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'insurance_AFP',
        'insurance_ISAPRE',
        'school_id',
        'rut',
        'name',
        'last_name',
        'birth_date',
        'address',
        'phone',
        'commune',
        'region',
        'nationality',
        'marital_status',
        'worker_type', //este es
        'function_worker',
        'load_hourly_work',
        'worker_titular',
        'settlement_date',
    ];

    const WORKER_TYPE_TEACHER = 1;
    const WORKER_TYPE_NON_TEACHER = 2;

    const WORKER_TYPES = [
        1 => "Docente",
        2 => "No Docente" 
    ];

    const FUNCTION_WORKER = [
        1 => 'Docente de aula',
        2 => 'Administrativo Calificado',
        3 => 'Auxiliar',
        5 => 'Docente superior (Inspector general)',
        6 => 'Docente superior (UTP)',
        7 => 'Docente superior (Director)',
        8 => 'Administrativo General',
        9 => 'Inspector Patio',
    ];

    const MARITAL_STATUS = [
        1 => "SOLTERO(A)",
        2 => "CASADO(A)",
        3 => "VIUDO(A)",
        4 => "SEPARADO(A)",
    ];

    public static function getWorkerTypes()
    {
        return self::WORKER_TYPES;
    }

    public function getDescriptionWorkerTypes()
    {
        return self::WORKER_TYPES[$this->worker_type] ?? "Desconocido";
    }

    public static function getFunctionWorkerTypes()
    {
        return self::FUNCTION_WORKER;
    }

    public function getFunctionWorkerDescription()
    {
        return static::FUNCTION_WORKER[$this->function_worker] ?? 'Desconocido';
    }

    public static function getMaritalStatusTypes()
    {
        return self::MARITAL_STATUS;
    }

    public function getMaritalStatusDescription()
    {
        return static::MARITAL_STATUS[$this->marital_status] ?? 'Desconocido';
    }

    public function getInsuranceNames()
    {
        $insuranceAFPName = Insurance::getNameInsurance($this->insurance_AFP);
        $insuranceISAPREName = Insurance::getNameInsurance($this->insurance_ISAPRE);

        return [
            'insurance_AFP' => $insuranceAFPName,
            'insurance_ISAPRE' => $insuranceISAPREName,
        ];
    }

    public static function createHourlyLoadArray(Request $request)
    {
        return [
            'lunes' => $request->input('carga_lunes', 0),
            'martes' => $request->input('carga_martes', 0),
            'miercoles' => $request->input('carga_miercoles', 0),
            'jueves' => $request->input('carga_jueves', 0),
            'viernes' => $request->input('carga_viernes', 0),
            'sabado' => $request->input('carga_sabado', 0),
        ];
    }

    public function updateHourlyLoad(array $loadHourlyWork)
    {
        $this->load_hourly_work = json_encode($loadHourlyWork);
        $this->save();
    }

    public static function createOrUpdateWorker(array $data, Worker $worker = null)
    {
        if ($worker) {
            $worker->update($data);
        } else {
            $worker = self::create($data);
        }

        return $worker;
    }

    public function getWorkerParameters($typeInsurance)
    {
        $parameterNames = [
            'COTIZACIONAFP',
            'APV',
            'AFPOTRO',
            'INSTITUCIONDESALUD',
            'COTIZACIONPACTADA',
        ];

        $parameters = Parameter::where('worker_id', $this->id)
            ->whereIn('name', $parameterNames)
            ->get()
            ->pluck('value', 'name')
            ->toArray();

        return [
            'success' => true,
            'insuranceType' => $typeInsurance,
            'cotizationAFP' => $parameters['COTIZACIONAFP'] ?? null,
            'apv' => $parameters['APV'] ?? null,
            'othersDiscounts' => $parameters['AFPOTRO'] ?? null,
            'institutionHealth' => $parameters['INSTITUCIONDESALUD'] ?? null,
            'pricePlan' => $parameters['COTIZACIONPACTADA'] ?? null,
            'unit' => Parameter::where('worker_id', $this->id)
                ->where('name', 'COTIZACIONPACTADA')->value('unit'),
            'workerType' => $this->getFunctionWorkerDescription(),
        ];
    }

    public static function getWorkersBySchoolAndType($schoolId, $type = null)
    {
        $query = self::where('settlement_date', null)
            ->where('school_id', $schoolId);

        // Permitir que el tipo sea nulo o diferente de 3
        if ($type !== null && $type != 3) {
            $query->where('worker_type', $type);
        }

        return $query->orderBy('last_name')
            ->orderBy('name')
            ->get(['id', 'name', 'last_name', 'worker_type']);
    }

    /**
     * Relación con la tabla 'insurances' (AFP).
     */
    public function insuranceAFP()
    {
        return $this->belongsTo(Insurance::class, 'insurance_AFP');
    }

    /**
     * Relación con la tabla 'insurances' (ISAPRE).
     */
    public function insuranceISAPRE()
    {
        return $this->belongsTo(Insurance::class, 'insurance_ISAPRE');
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    public function parameters()
    {
        return $this->hasMany(Parameter::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function liquidations()
    {
        return $this->hasMany(Liquidation::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class, 'worker_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
