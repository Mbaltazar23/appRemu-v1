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

    /**
     * Método para insertar o actualizar parámetros según el tipo de seguro.
     */
    public static function updateOrInsertInsuranceParams($workerId, $insuranceType, $schoolId, $insuranceID, $extraParams = [])
    {
        // Obtener la cotización del seguro
        $cotizacion = Insurance::getCotizationInsurance($insuranceID);

        // Inicializamos los parámetros comunes
        $params = [];

        // Definir los parámetros comunes por tipo de seguro
        if ($insuranceType != Insurance::AFP) {
            $params = [
                'ISAPRETRABAJADOR' => ['description' => $insuranceID, 'unit' => null, 'value' => 1],
                'COTIZACIONISAPRE' => ['description' => 'Cotización en ISAPRE del trabajador', 'unit' => null, 'value' => $cotizacion],
                'COTIZACIONPACTADA' => isset($extraParams['cotization']) ?
                ['description' => 'Cotización pactada en ISAPRE del trabajador', 'unit' => $extraParams['unit'], 'value' => $extraParams['cotization']] : null,
                'ISAPREOTRO' => isset($extraParams['others_discounts']) ? ['description' => 'Otro descuento en ISAPRE', 'unit' => null, 'value' => $extraParams['others_discounts']] : null,
            ];
        } else {
            $params = [
                'AFPTRABAJADOR' => ['description' => $insuranceID, 'unit' => null, 'value' => 1],
                'COTIZACIONAFP' => ['description' => 'Cotización en AFP del trabajador', 'unit' => null, 'value' => $cotizacion],
                'APV' => isset($extraParams['apv']) ? ['description' => 'APV', 'unit' => $extraParams['unit'], 'value' => $extraParams['apv']] : null,
                'AFPOTRO' => isset($extraParams['others_discounts']) ? ['description' => 'Otro descuento en AFP', 'unit' => null, 'value' => $extraParams['others_discounts']] : null,
            ];
        }

        // Filtramos cualquier parámetro que sea null (no se pasa)
        $params = array_filter($params);

        // Insertar o actualizar los parámetros
        foreach ($params as $paramName => $paramData) {
            // Buscar si el parámetro ya existe
            $existingParam = self::where('name', $paramName)
                ->where('worker_id', $workerId)
                ->where('school_id', $schoolId)
                ->first();

            if ($existingParam) {
                // Actualizar el parámetro existente
                $existingParam->update($paramData);
            } else {
                // Crear un nuevo parámetro si no existe
                self::create(array_merge(['name' => $paramName, 'worker_id' => $workerId, 'school_id' => $schoolId], $paramData));
            }
        }

        return "Se han actualizado los parámetros para el trabajador en la institución.";
    }

    public static function deleteParameters($workerId, $schoolId, $insuranceType)
    {
        // Eliminar parámetros relacionados con AFP o ISAPRE
        $parametersToDelete = [];

        if ($insuranceType === 'AFP') {
            // Parámetros para AFP
            $parametersToDelete = ['COTIZACIONAFP', 'APV', 'AFPOTRO'];
        } elseif ($insuranceType === 'ISAPRE') {
            // Parámetros para ISAPRE
            $parametersToDelete = ['COTIZACIONISAPRE', 'COTIZACIONPACTADA', 'ISAPREOTRO'];
        }

        // Eliminar todos los parámetros relacionados con el tipo de seguro
        foreach ($parametersToDelete as $param) {
            Parameter::where('name', $param)
                ->where('worker_id', $workerId)
                ->where('school_id', $schoolId)
                ->delete();
        }

        // Eliminar el parámetro específico de tipo trabajador
        $workerParam = ($insuranceType === 'AFP') ? 'AFPTRABAJADOR' : 'ISAPRETRABAJADOR';
        Parameter::where('name', $workerParam)
            ->where('worker_id', $workerId)
            ->where('school_id', $schoolId)
            ->delete();
    }

    public function createParameter(array $data)
    {
        return self::create($data);
    }

    public static function updateParamValue($classId, $schoolId, $value, $workerId = 0)
    {
        $query = self::where('name', $classId)
            ->where('school_id', $schoolId);

        if ($workerId !== 0) {
            $query->where('worker_id', $workerId);
        }

        $query->update(['value' => $value]);
    }

    public static function updateOrInsertParamValue($classId, $workerId = 0, $school_id = 0, $description = "", $value)
    {
        // Crea la consulta básica buscando por el nombre (classId)
        $query = self::where('name', $classId)->where('school_id', $school_id);

        if ($workerId != 0) {
            $query->where('worker_id', $workerId);
        }
        // Ejecuta la consulta para obtener el parámetro
        $param = $query->first();

        if ($param) {
            // Si existe, actualiza
            $param->update(['value' => $value, 'updated_at' => now()]);
        } else {
            // Si no existe, crea uno nuevo
            self::create([
                'name' => $classId,
                'worker_id' => $workerId,
                'school_id' => $school_id,
                'description' => $description,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public static function createOrUpdateParamIndicators($classId, $value)
    {
        // Crea la consulta básica buscando por el nombre (classId) y el school_id
        $query = self::where('name', $classId);
        // Ejecuta la consulta para obtener el parámetro
        $param = $query->first();
        // Si existe el parámetro, realiza un update
        if ($param) {
            $param->update([
                'value' => $value,
                'updated_at' => now(), // Actualiza el timestamp
            ]);
        } else {
            // Si no existe el parámetro, crea uno nuevo
            self::create([
                'name' => $classId,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
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
        // Start the query with the 'name' filter
        $query = self::where('name', $name)
            ->where('school_id', $schoolId)
            ->where(function ($query) use ($workerId) {
                $query->where('worker_id', $workerId)
                    ->orWhere('worker_id', 0); // Mimicking SQL 'worker_id=0' condition
            });
        return $query->value('value');
    }

    public static function exists($name, $worker_id, $school_id)
    {
        return self::where('name', $name)
            ->where('worker_id', $worker_id)
            ->where('school_id', $school_id)
            ->exists();
    }

    public static function getTitleTuition($tuition_id, $school_id)
    {
        $result = self::where('name', $tuition_id)
            ->where('school_id', $school_id)
            ->first(['value']);

        return $result->name;
    }

    public static function getWorkerParametersByInsuranceType($workerId, $schoolId, $insuranceType)
    {
        $isAfp = $insuranceType !== Insurance::ISAPRE;

        $keys = $isAfp ? [
            'AFPTRABAJADOR' => 'insurance_id',
            'COTIZACIONAFP' => 'cotizacion_afp',
            'APV' => 'apv',
            'AFPOTRO' => 'others_discounts',
        ] : [
            'ISAPRETRABAJADOR' => 'insurance_id',
            'COTIZACIONISAPRE' => 'cotization_isapre',
            'ISAPREOTRO' => 'others_discounts',
        ];

        $parameters = [];

        foreach ($keys as $paramName) {
            $parameters[$paramName] = self::getValueByName($paramName, $schoolId, $workerId);
        }

        return $parameters;
    }

    public static function getTitleByParameter($tuition_id, $workerId, $schoolId)
    {
        // Check if classId is empty
        if ($tuition_id == "") {
            return '';
        }
        // Get the description associated with the classId and schoolId
        $classDescription = self::getTuitionDescription($tuition_id, $schoolId);

        if ($classDescription == "") {
            return Tuition::getTuitionTitle($tuition_id, $schoolId);
        } else {
            // If class description exists, return the parameter description
            return self::getDescriptionByTuitionWorkerAndSchool($classDescription, $workerId, $schoolId);
        }
    }

    public static function getTuitionDescription($tuition_id, $school_id)
    {
        return Tuition::where('tuition_id', $tuition_id)->where('school_id', $school_id)->value('description') ?? "";
    }

    public static function getDescriptionByCode($name, $worker_id, $school_id)
    {
        return self::where('name', $name)->where('worker_id', $worker_id)->where('school_id', $school_id)->value('description');
    }

    // Método para obtener el valor de parámetro del trabajador y colegio al que pertenece
    public static function getParameterValue($name, $workerId, $schoolId)
    {
        // Usamos el query builder con raw SQL cuando es necesario
        $query = self::where('name', $name)
            ->whereRaw('(school_id = ? OR school_id = 0)', $schoolId) // Usamos raw para la condición OR
            ->whereRaw('(worker_id = ? OR worker_id = 0)', $workerId) // Usamos raw para la condición OR
            ->orderByDesc('worker_id');
        // Ejecutamos la consulta y obtenemos el primer valor
        $parameter = $query->first();

        return $parameter->value ?? 0; // Si no existe, devolvemos 0
    }

    // Método para obtener la unidad de parámetro por clase, tipo de trabajador y colegio
    public static function getUnitByTuitionWorkerAndSchool($tuitionId, $workerId, $schoolId)
    {
        $query = self::where('name', $tuitionId)
            ->whereRaw('(school_id = ? OR school_id = 0)', $schoolId)
            ->whereRaw('(worker_id = ? OR worker_id = 0)', $workerId);
        // Ejecutamos la consulta y obtenemos el primer resultado
        $parameter = $query->first();

        return $parameter->unit ?? ""; // Si no existe, devolvemos ""
    }

    // Método para obtener la suma del valor de los parámetros
    public static function getSumValueByTuitionSchoolAndWorkerType($tuitionId, $schoolId, $workerTypeId)
    {
        $query = self::selectRaw('SUM(parameters.value) as total_value')
            ->leftJoin('workers', 'parameters.worker_id', '=', 'workers.id') // Realizamos un LEFT JOIN con la tabla workers
            ->where('parameters.name', $tuitionId)
            ->where('parameters.school_id', $schoolId)
            ->where('workers.worker_type', $workerTypeId)
            ->whereRaw('workers.settlement_date IS NULL'); // Aseguramos que settlement_date sea NULL

        // Ejecutamos la consulta y obtenemos el valor total
        $result = $query->first();

        return $result->total_value ?? 0; // Si no existe, devolvemos 0
    }

    // Método para obtener la descripción de un parámetro
    public static function getDescriptionByTuitionWorkerAndSchool($tuitionId, $workerId, $schoolId)
    {
        // Si el ID de clase está vacío, retornar cadena vacía.
        if ($tuitionId == "") {
            return "";
        }
        // Usando Eloquent para manejar las condiciones con orWhere
        $parameter = self::where('name', $tuitionId)
            ->where('school_id', $schoolId) // Filtramos por el colegio
            ->whereRaw('(worker_id = ? OR worker_id = 0)', [$workerId])
            ->get();
        // Si existe un parámetro, retornar su descripción. Si no, retornar cadena vacía.
        return $parameter->first()->description ?? "";
    }

    public static function getParametersWithTuitions($name, $schoolId, $workerId)
    {
        return self::select(
            'tuitions.title',
            'tuitions.tuition_id',
            'parameters.name',
            'parameters.description',
            'parameters.unit',
            'parameters.value',
            'tuitions.type',
            'tuitions.in_liquidation'
        )
            ->leftJoin('tuitions', 'tuitions.tuition_id', '=', 'parameters.name')
            ->where('parameters.worker_id', $workerId)
            ->where('parameters.school_id', $schoolId)
            ->where('parameters.name', $name)
            ->first();
    }

    public static function getParametersBySchoolAndName($schoolId, $name)
    {
        return self::select(
            'parameters.name',
            'parameters.description',
            'parameters.unit',
            'parameters.value',
            'tuitions.type',
            'parameters.worker_id'
        )
            ->leftJoin('tuitions', 'tuitions.tuition_id', '=', 'parameters.name')
            ->where('parameters.school_id', $schoolId)
            ->where('parameters.name', $name)
            ->get();
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
