<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Parameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id',
        'worker_id',
        'description',
        'unit',
        'start_date',
        'end_date',
        'value',
    ];

    public static function insertParameters($workerId, Request $request, $schoolId)
    {
        $params = [
            'CARGASFAMILIARES' => 'num_load_family',
            'CARGAHORARIA' => 'hourly_load',
            'TIPOCONTRATO' => 'contract_type',
            'YEARINICIOSERVICIO' => 'service_start_year',
            'ADHIEREASEGURO' => 'unemployment_insurance',
            'JUBILADO' => 'retired',
        ];

        foreach ($params as $name => $input) {
            self::updateOrCreate(['worker_id' => $workerId, 'name' => $name, 'school_id' => $schoolId], ['value' => $request->input($input)]);
        }

        if ($request->input('worker_type') == Worker::WORKER_TYPE_TEACHER) {
            self::updateOrCreate(['worker_id' => $workerId, 'name' => 'HORASPERFECCIONAMIENTO', 'school_id' => $schoolId], ['value' => 0]);
            self::updateOrCreate(['worker_id' => $workerId, 'name' => 'DESEMPEÑODEEXCELENCIA', 'school_id' => $schoolId], ['value' => 0]);
        } else {
            self::updateOrCreate(['worker_id' => $workerId, 'name' => 'SUELDOBASEB', 'school_id' => $schoolId], ['value' => $request->input('base_salary')]);
        }
    }

    public static function updateWorkerParametersInsurance($workerId, $insuranceType, $params, $schoolId)
    {
        $isAfp = $insuranceType !== Insurance::ISAPRE;
        $keys = $isAfp ? [
            'AFPTRABAJADOR' => 'cotization_afp',
            'COTIZACIONAFP' => 'cotizacion_afp',
            'APV' => 'apv',
            'AFPOTRO' => 'others_discounts',
        ] : [
            'INSTITUCIONDESALUD' => 'institution_health',
        ];

        foreach ($keys as $name => $paramKey) {
            $value = $params[$paramKey] ?? null;
            self::updateOrCreate(
                ['worker_id' => $workerId, 'name' => $name, 'school_id' => $schoolId],
                ['value' => $value, 'description' => $isAfp ? null : 'Institución de salud']
            );
        }

        if ($isAfp) {
            $cotizacionAfp = Parameter::obt_cotizacion_afp($params['cotization_afp'] ?? null);
            self::updateOrCreate(
                ['worker_id' => $workerId, 'name' => 'COTIZACIONAFP', 'school_id' => $schoolId],
                ['value' => $cotizacionAfp, 'description' => 'Cotización en AFP del trabajador']
            );
        } else {
            $institution = $params['institution_health'];
            $pricePlan = $params['price_plan'] ?? null;
            $unidad = $params['unit'] ?? null;

            if (strpos($institution, "FONASA") !== false) {
                Parameter::where('name', 'COTIZACIONPACTADA')->where('worker_id', $workerId)->where('school_id', $schoolId)->delete();
                self::updateOrCreate(
                    ['worker_id' => $workerId, 'name' => 'COTIZACIONFONASA', 'school_id' => $schoolId],
                    ['value' => 'Cotización Fonasa']
                );
            } else {
                Parameter::where('name', 'COTIZACIONFONASA')->where('worker_id', $workerId)->where('school_id', $schoolId)->delete();
                self::updateOrCreate(
                    ['worker_id' => $workerId, 'name' => 'COTIZACIONPACTADA', 'school_id' => $schoolId],
                    ['description' => 'Cotización pactada isapre', 'value' => $pricePlan, 'unit' => $unidad]
                );
            }
        }
    }

    public function createParameter(array $data)
    {
        return self::create($data);
    }

    public static function obt_cotizacion_afp($afp)
    {
        $cotizacion = self::where('name', 'COTIZACIONAFP')
            ->where('value', $afp)
            ->first();

        return $cotizacion ? $cotizacion->value : null; // Retorna el valor de cotización o null si no existe
    }

    public static function updateParamValue($classId, $schoolId, $value)
    {
        self::where('name', $classId)
            ->where('school_id', $schoolId)
            ->update(['value' => $value]);
    }

    public static function updateOrInsertParamValue($classId, $value)
    {
        $param = self::where('name', $classId)->first();

        if ($param) {
            // Si existe, actualiza
            $param->update(['value' => $value]);
        } else {
            // Si no existe, crea uno nuevo
            self::create([
                'name' => $classId,
                'value' => $value,
                'created_at' => now(), // Establece la fecha de creación
                'updated_at' => now(), // Establece la fecha de actualización
            ]);
        }
    }

    public static function deleteParamAll($name, $schoolId)
    {
        self::where('name', $name)
            ->where('school_id', $schoolId)->delete();
    }

    public static function updateParamsSchool_All($name, $schoolId, $value)
    {
        self::where('name', $name)
            ->where('school_id', $schoolId)
            ->update(['value' => $value, 'updated_at' => now()]);
    }

    // Método para obtener un valor específico basado en el nombre
    public static function getValueByName($name, $schoolId, $workerId)
    {
        return self::where('name', $name)
            ->value('value');
    }

    public static function exists($name, $worker_id, $school_id)
    {
        return self::where('name', $name)
            ->where('worker_id', $worker_id)
            ->where('school_id', $school_id)
            ->exists();
    }

    public static function getParameterValue($name, $workerId, $schoolId)
    {
        $result = self::where('name', $name)
            ->where(function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)
                    ->orWhere('school_id', 0);
            })
            ->where(function ($query) use ($workerId) {
                $query->where('worker_id', $workerId)
                    ->orWhere('worker_id', 0);
            })
            ->orderBy('worker_id', 'desc')
            ->first(['value']);

        return $result ? $result->value : 0;
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}
