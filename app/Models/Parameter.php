<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Parameter extends Model
{

    use HasFactory;

    // Specifies which fields are mass assignable
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

    /**
     * Insert parameters related to the worker from the request data.
     * It updates or creates new parameters based on predefined mappings and the worker type.
     *
     * @param int $workerId The ID of the worker.
     * @param Request $request The HTTP request containing the data.
     * @param int $schoolId The ID of the school.
     */
    public static function insertParameters($workerId, Request $request, $schoolId)
    {
        // Define mappings of parameter names to input fields from the request.
        $params = [
            'CARGASFAMILIARES'   => 'num_load_family',
            'CARGAHORARIA'       => 'hourly_load',
            'TIPOCONTRATO'       => 'contract_type',
            'YEARINICIOSERVICIO' => 'service_start_year',
            'ADHIEREASEGURO'     => 'unemployment_insurance',
            'JUBILADO'           => 'retired',
        ];

        // Loop over the parameters and either update or create them.
        foreach ($params as $name => $input) {
            self::updateOrCreate(['worker_id' => $workerId, 'name' => $name, 'school_id' => $schoolId], ['value' => $request->input($input)]);
        }

        // Additional logic for worker type-specific parameters
        if ($request->input('worker_type') == Worker::WORKER_TYPE_TEACHER) {
            self::updateOrCreate(['worker_id' => $workerId, 'name' => 'HORASPERFECCIONAMIENTO', 'school_id' => $schoolId], ['value' => 0]);
            self::updateOrCreate(['worker_id' => $workerId, 'name' => 'DESEMPEÑODEEXCELENCIA', 'school_id' => $schoolId], ['value' => 0]);
        } else {
            self::updateOrCreate(['worker_id' => $workerId, 'name' => 'SUELDOBASEB', 'school_id' => $schoolId], ['value' => $request->input('base_salary')]);
        }
    }

    /**
     * Updates or inserts insurance-related parameters based on the worker's insurance type.
     *
     * @param int $workerId The worker's ID.
     * @param int $insuranceType The type of insurance.
     * @param int $schoolId The school ID.
     * @param int $insuranceID The ID of the insurance.
     * @param array $extraParams Additional parameters passed for customization.
     * @return string A message indicating the operation status.
     */
    public static function updateOrInsertInsuranceParams($workerId, $insuranceType, $schoolId, $insuranceID, $extraParams = [])
    {
        // Fetch the insurance quote based on insurance ID.
        $cotizacion = Insurance::getCotizationInsurance($insuranceID);
        $params     = [];

        // Define insurance parameters based on the type of insurance (AFP or ISAPRE).
        if ($insuranceType != Insurance::AFP) {
            // ISAPRE parameters
            $params = [
                'ISAPRETRABAJADOR'  => ['description' => $insuranceID, 'unit' => null, 'value' => 1],
                'COTIZACIONISAPRE'  => ['description' => 'Cotización en ISAPRE del trabajador', /* 'unit' => $extraParams['unit_cotizacionisapre'] ?? null, */'value' => $cotizacion],
                'COTIZACIONPACTADA' => isset($extraParams['cotization']) ?
                ['description' => 'Cotización pactada en ISAPRE del trabajador', 'unit' => $extraParams['unit'] != "UF" ? NULL : $extraParams['unit'], 'value' => $extraParams['cotization']] : null,
                'ISAPREOTRO'        => isset($extraParams['others_discounts']) ? ['description' => 'Otro descuento en ISAPRE', 'unit' => null, 'value' => $extraParams['others_discounts']] : null,
            ];
        } else {
            // AFP parameters
            $params = [
                'AFPTRABAJADOR' => ['description' => $insuranceID, 'unit' => null, 'value' => 1],
                'COTIZACIONAFP' => ['description' => 'Cotización en AFP del trabajador', /* 'unit' => $extraParams['unit_cotizacionafp'] ?? null, */'value' => $cotizacion],
                'APV'           => isset($extraParams['apv']) ? ['description' => 'APV', 'unit' => $extraParams['unit'] != "UF" ? NULL : $extraParams['unit'], 'value' => $extraParams['apv']] : null,
                'AFPOTRO'       => isset($extraParams['others_discounts']) ? ['description' => 'Otro descuento en AFP', 'unit' => null, 'value' => $extraParams['others_discounts']] : null,
            ];
        }
        // Filter out null parameters and update or create the parameters.
        $params = array_filter($params);
        foreach ($params as $paramName => $paramData) {
            $existingParam = self::where('name', $paramName)
                ->where('worker_id', $workerId)
                ->where('school_id', $schoolId)
                ->first();

            if ($existingParam) {
                $existingParam->update($paramData);
            } else {
                self::create(array_merge(['name' => $paramName, 'worker_id' => $workerId, 'school_id' => $schoolId], $paramData));
            }
        }

        return "Se han actualizado parámetros para el trabajador de la institución.";
    }

    /**
     * Updates or inserts both AFP and ISAPRE insurance parameters for a worker.
     *
     * This method checks if the cotization values for AFP and ISAPRE exceed the defined limit
     * before inserting or updating the parameters. It processes both types of insurance (AFP and ISAPRE)
     * simultaneously using the provided insurance IDs and handles the logic for updating or creating
     * the respective parameters.
     *
     * @param int $workerId The worker's ID.
     * @param int $schoolId The school ID.
     * @param string $insuranceID_AFP The insurance ID for AFP.
     * @param string $insuranceID_ISAPRE The insurance ID for ISAPRE.
     * @param array $extraParams Additional parameters such as cotization values, APV, etc.
     * @return string Success message indicating that both insurance parameters have been updated or inserted.
     */
    public static function updateOrInsertBothInsuranceParams($workerId, $schoolId, $insuranceID_AFP, $insuranceID_ISAPRE, $extraParams = [])
    {
        // Fetch the insurance quotes based on insurance IDs for both AFP and ISAPRE
        $cotizacionAFP    = Insurance::getCotizationInsurance($insuranceID_AFP);
        $cotizacionISAPRE = Insurance::getCotizationInsurance($insuranceID_ISAPRE);

        // Initialize an array to hold all insurance parameters (for both AFP and ISAPRE)
        $params = [];

        // Check the cotization limits for both AFP and ISAPRE before proceeding
        $afpValidation    = self::checkCotizationUnitLimit($workerId, $schoolId, $extraParams['unit'] ?? 'UF', $cotizacionAFP, Insurance::AFP);
        $isapreValidation = self::checkCotizationUnitLimit($workerId, $schoolId, $extraParams['unit'] ?? 'UF', $cotizacionISAPRE, Insurance::ISAPRE);

        if ($afpValidation['status'] == 'error') {
            return $afpValidation; // Return error if AFP cotization exceeds limit
        }

        if ($isapreValidation['status'] == 'error') {
            return $isapreValidation; // Return error if ISAPRE cotization exceeds limit
        }

        // Define AFP parameters
        $params['AFPTRABAJADOR'] = [
            'description' => $insuranceID_AFP,
            'unit'        => null,
            'value'       => 1,
        ];
        $params['COTIZACIONAFP'] = [
            'description' => 'Cotización en AFP del trabajador',
            'unit'        => null,
            'value'       => $cotizacionAFP,
        ];
        $params['APV'] = isset($extraParams['apv']) ? [
            'description' => 'APV',
            'unit'        => $extraParams['unit']  != "UF" ?? NULL,
            'value'       => $extraParams['apv'],
        ] : null;
        $params['AFPOTRO'] = isset($extraParams['others_discounts']) ? [
            'description' => 'Otro descuento en AFP',
            'unit'        => null,
            'value'       => $extraParams['others_discounts'],
        ] : null;

        // Define ISAPRE parameters
        $params['ISAPRETRABAJADOR'] = [
            'description' => $insuranceID_ISAPRE,
            'unit'        => null,
            'value'       => 1,
        ];
        $params['COTIZACIONISAPRE'] = [
            'description' => 'Cotización en ISAPRE del trabajador',
            'unit'        => null,
            'value'       => $cotizacionISAPRE,
        ];
        $params['COTIZACIONPACTADA'] = isset($extraParams['cotization']) ? [
            'description' => 'Cotización pactada en ISAPRE del trabajador',
            'unit'        => $extraParams['unit'] != "UF" ?? NULL,
            'value'       => $extraParams['cotization'],
        ] : null;
        $params['ISAPREOTRO'] = isset($extraParams['others_discounts']) ? [
            'description' => 'Otro descuento en ISAPRE',
            'unit'        => null,
            'value'       => $extraParams['others_discounts'],
        ] : null;

        // Filter out null parameters
        $params = array_filter($params);

        // Loop through each parameter and update or insert as needed
        foreach ($params as $paramName => $paramData) {
            $existingParam = self::where('name', $paramName)
                ->where('worker_id', $workerId)
                ->where('school_id', $schoolId)
                ->first();

            if ($existingParam) {
                // If the parameter already exists, update it
                $existingParam->update($paramData);
            } else {
                // If the parameter doesn't exist, create it
                self::create(array_merge(['name' => $paramName, 'worker_id' => $workerId, 'school_id' => $schoolId], $paramData));
            }
        }
    }

    /**
     * Checks if the cotization value (in UF) exceeds a limit (50 times the UF value).
     *
     * @param int $workerId The worker's ID.
     * @param int $schoolId The school ID.
     * @param string $unitType The unit of cotization (UF or Pesos).
     * @param float $cotization The cotization value.
     * @param int $insuranceType The insurance type (AFP or ISAPRE).
     * @return array Result of the check (error or success).
     */
    public static function checkCotizationUnitLimit($workerId, $schoolId, $unitType, $cotization, $insuranceType)
    {
        // Get the current value of UF.
        $ufValue = self::getParameterValue('UF', $workerId, $schoolId);
        $limit   = $ufValue * 50;

        $cotizationName = $insuranceType != Insurance::AFP ? "La cotización" : "El Ahorro Previsional Voluntario (APV)";
        if ($unitType == 'UF') {
            $cotization *= $ufValue; // Convert to pesos if the unit is UF.
        }

        if ($cotization > $limit) {
            return [
                'status'  => 'error',
                'message' => "$cotizationName no puede exceder 50 veces el valor actual de UF.",
            ];
        }

        return ['status' => 'success'];
    }

    /**
     * Deletes parameters related to the worker based on the insurance type.
     * Removes specific parameters associated with AFP or ISAPRE.
     *
     * @param int $workerId The worker's ID.
     * @param int $schoolId The school ID.
     * @param int $insuranceType The insurance type (AFP or ISAPRE).
     */
    public static function deleteParameters($workerId, $schoolId, $insuranceType)
    {
        // Define parameters to be deleted based on the insurance type.
        $parametersToDelete = [];

        if ($insuranceType == Insurance::AFP) {
            $parametersToDelete = ['COTIZACIONAFP', 'APV', 'AFPOTRO'];
        } else {
            $parametersToDelete = ['COTIZACIONISAPRE', 'COTIZACIONPACTADA', 'ISAPREOTRO'];
        }

        // Delete the parameters.
        foreach ($parametersToDelete as $param) {
            Parameter::where('name', $param)
                ->where('worker_id', $workerId)
                ->where('school_id', $schoolId)
                ->delete();
        }

        // Delete the worker-specific insurance type parameter.
        $workerParam = ($insuranceType == Insurance::AFP) ? 'AFPTRABAJADOR' : 'ISAPRETRABAJADOR';
        Parameter::where('name', $workerParam)
            ->where('worker_id', $workerId)
            ->where('school_id', $schoolId)
            ->delete();

        // Update the worker's insurance status.
        $worker = Worker::find($workerId);
        if ($insuranceType != Insurance::ISAPRE) {
            $worker->insurance_AFP = null;
        } else {
            $worker->insurance_ISAPRE = null;
        }
        $worker->save();
    }

    /**
     * Creates a new parameter with the provided data.
     *
     * @param array $data Data to create the parameter.
     * @return \App\Models\Parameter The created parameter.
     */
    public function createParameter(array $data)
    {
        return self::create($data);
    }

    /**
     * Updates the value of a specific parameter.
     *
     * @param string $classId The parameter name.
     * @param int $schoolId The school ID.
     * @param mixed $value The new value for the parameter.
     * @param int $workerId The worker's ID (optional).
     */
    public static function updateParamValue($tuitionId, $schoolId, $value, $workerId = 0)
    {
        $query = self::where('name', $tuitionId)
            ->where('school_id', $schoolId);

        if ($workerId !== 0) {
            $query->where('worker_id', $workerId);
        }

        $query->update(['value' => $value]);
    }

    /**
     * This method either updates the value of an existing parameter or inserts a new one if it does not exist.
     * It searches for a parameter using the `classId`, `workerId`, and `school_id` and updates its value.
     * If the parameter does not exist, it creates a new one with the provided details.
     *
     * @param string $classId The name of the parameter to update or insert.
     * @param int $workerId The ID of the worker associated with the parameter (optional, defaults to 0).
     * @param int $school_id The ID of the school associated with the parameter (optional, defaults to 0).
     * @param string $description A description for the parameter (optional).
     * @param mixed $value The value to assign to the parameter.
     */
    public static function updateOrInsertParamValue($tuitionId, $workerId = 0, $school_id = 0, $description = "", $value)
    {
        // Create the basic query searching by the parameter name (classId) and school_id
        $query = self::where('name', $tuitionId)->where('school_id', $school_id);
        // If a worker_id is provided, add it to the query filter
        if ($workerId != 0) {
            $query->where('worker_id', $workerId);
        }
        // Execute the query to find the parameter
        $param = $query->first();
        // We evaluate if the parameter exists
        if ($param) {
            // If the parameter exists, update it with the new value and update the 'updated_at' timestamp
            $param->update(['value' => $value, 'updated_at' => now()]);
        } else {
            // If the parameter doesn't exist, create a new one with the provided data
            self::create([
                'name'        => $tuitionId,
                'worker_id'   => $workerId,
                'school_id'   => $school_id,
                'description' => $description,
                'value'       => $value,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    /**
     * This method deletes all parameters with the given name and school_id.
     * It removes the parameter entries from the database for a specific school.
     */
    public static function deleteParamAll($name, $schoolId)
    {
        self::where('name', $name)
            ->where('school_id', $schoolId)->delete();
    }

    /**
     * This method updates the value of a specific parameter for all records with the given name and school_id.
     * It also updates the `updated_at` timestamp to reflect the time of the change.
     */
    public static function updateParamsSchool_All($name, $schoolId, $value)
    {
        self::where('name', $name)
            ->where('school_id', $schoolId)
            ->update(['value' => $value, 'updated_at' => now()]);
    }

    /**
     * This method checks if a parameter with the given name exists for a specific worker in a specific school.
     * It returns a boolean indicating whether the parameter exists.
     */
    public static function exists($name, $worker_id, $school_id)
    {
        return self::where('name', $name)
            ->where('worker_id', $worker_id)
            ->where('school_id', $school_id)
            ->exists();
    }

    /**
     * This method retrieves worker parameters based on the insurance type (AFP or ISAPRE).
     * It returns an array containing different parameter values like cotización, APV, discounts, etc.
     */
    public static function getWorkerParametersByInsuranceType(Worker $worker, $type)
    {
        if (! $worker) {
            return [
                'cotizacionafp'         => 0,
                'apv'                   => 0,
                'others_discounts'      => 0,
                'unidad'                => 'Pesos',
                'cotizacionisapre'      => 0,
                'cotizacionpactada'     => 0,
                'unit_cotizacionafp'    => 'Pesos', // Default value for AFP
                'unit_cotizacionisapre' => 'Pesos', // Default value for ISAPRE
            ];
        }

        if ($type == Insurance::AFP) {
            return [
                'cotizacionafp'      => $worker->parameters->where('name', 'COTIZACIONAFP')->first()->value ?? 0,
                'apv'                => $worker->parameters->where('name', 'APV')->first()->value ?? 0,
                'others_discounts'   => $worker->parameters->where('name', 'AFPOTRO')->first()->value ?? 0,
                'unidad'             => $worker->parameters->where('name', 'APV')->value('unit') ?? 'Pesos',
                'unit_cotizacionafp' => $worker->parameters->where('name', 'COTIZACIONAFP')->first()->unit ?? 'Pesos', // Get AFP cotization unit
            ];
        } else {
            return [
                'cotizacionisapre'      => $worker->parameters->where('name', 'COTIZACIONISAPRE')->first()->value ?? 0,
                'cotizacionpactada'     => $worker->parameters->where('name', 'COTIZACIONPACTADA')->first()->value ?? 0,
                'others_discounts'      => $worker->parameters->where('name', 'ISAPREOTRO')->first()->value ?? 0,
                'unidad'                => $worker->parameters->where('name', 'COTIZACIONPACTADA')->first()->unit ?? 'Pesos',
                'unit_cotizacionisapre' => $worker->parameters->where('name', 'COTIZACIONISAPRE')->first()->unit ?? 'Pesos', // Get ISAPRE cotization unit
            ];
        }
    }

    /**
     * This method retrieves the title of a class based on its tuition_id, workerId, and schoolId.
     * If a description exists, it returns that description; otherwise, it retrieves the title from the tuition.
     */
    public static function getTitleByParameter($tuition_id, $workerId, $schoolId)
    {
        if ($tuition_id == "") {
            return '';
        }
        $classDescription = self::getTuitionDescription($tuition_id, $schoolId);
        if ($classDescription == "") {
            return Tuition::getTuitionTitle($tuition_id, $schoolId);
        } else {
            return self::getDescriptionByTuitionWorkerAndSchool($classDescription, $workerId, $schoolId);
        }
    }

    /**
     * This method retrieves the description of a tuition based on tuition_id and school_id.
     * If the description doesn't exist, it returns an empty string.
     */
    public static function getTuitionDescription($tuition_id, $school_id)
    {
        return Tuition::where('tuition_id', $tuition_id)->where('school_id', $school_id)->value('description') ?? "";
    }

    /**
     * This method retrieves the description associated with a specific code (name).
     * It returns the description of the parameter by searching for its name, worker_id, and school_id.
     */
    public static function getDescriptionByCode($name, $worker_id, $school_id)
    {
        return self::where('name', $name)->where('worker_id', $worker_id)->where('school_id', $school_id)->value('description');
    }

    /**
     * This method retrieves the value of a parameter for a worker based on the parameter name, worker_id, and school_id.
     * It returns the parameter's value or 0 if the parameter doesn't exist.
     */
    public static function getParameterValue($name, $workerId, $schoolId)
    {
        $query = self::where('name', $name)
            ->whereRaw('(school_id = ? OR school_id = 0)', $schoolId) // Using raw SQL for OR condition
            ->whereRaw('(worker_id = ? OR worker_id = 0)', $workerId) // Using raw SQL for OR condition
            ->orderByDesc('worker_id');
        $parameter = $query->first();

        return $parameter->value ?? 0; // If not found, return 0
    }

    /**
     * This method retrieves the unit associated with a parameter, given the tuitionId, workerId, and schoolId.
     * It returns the unit of the parameter or an empty string if not found.
     */
    public static function getUnitByTuitionWorkerAndSchool($tuitionId, $workerId, $schoolId)
    {
        $query = self::where('name', $tuitionId)
            ->whereRaw('(school_id = ? OR school_id = 0)', $schoolId)
            ->whereRaw('(worker_id = ? OR worker_id = 0)', $workerId);
        $parameter = $query->first();

        return $parameter->unit ?? ""; // If not found, return empty string
    }

    /**
     * This method calculates the sum of the parameter values based on the tuitionId, schoolId, and workerTypeId.
     * It returns the total sum of the parameter values.
     */
    public static function getSumValueByTuitionSchoolAndWorkerType($tuitionId, $schoolId, $workerTypeId)
    {
        $query = self::selectRaw('SUM(parameters.value) as total_value')
            ->leftJoin('workers', 'parameters.worker_id', '=', 'workers.id') // Perform LEFT JOIN with workers table
            ->where('parameters.name', $tuitionId)
            ->where('parameters.school_id', $schoolId)
            ->where('workers.worker_type', $workerTypeId)
            ->whereRaw('workers.settlement_date IS NULL'); // Ensure settlement_date is NULL

        $result = $query->first();

        return $result->total_value ?? 0; // If not found, return 0
    }

    /**
     * This method retrieves the description of a parameter based on tuitionId, workerId, and schoolId.
     * If the parameter is not found, it returns an empty string.
     */
    public static function getDescriptionByTuitionWorkerAndSchool($tuitionId, $workerId, $schoolId)
    {
        if ($tuitionId == "") {
            return "";
        }
        $parameter = self::where('name', $tuitionId)
            ->where('school_id', $schoolId)
            ->whereRaw('(worker_id = ? OR worker_id = 0)', [$workerId])
            ->get();

        return $parameter->first()->description ?? "";
    }

    /**
     * This method defines the relationship between the Parameter model and the School model.
     * It represents the fact that a parameter belongs to a specific school.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * This method defines the relationship between the Parameter model and the Worker model.
     * It represents the fact that a parameter belongs to a specific worker.
     */
    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

}
