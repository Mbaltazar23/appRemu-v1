<?php

namespace App\Imports;

use App\Http\Requests\WorkerFormRequest;
use App\Models\Contract;
use App\Models\Insurance;
use App\Models\Parameter;
use App\Models\Tuition;
use App\Models\Worker;
use App\Services\ConvertDateService;
use App\Helpers\ConvertNumberToWords;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WorkersImport implements ToCollection, WithHeadingRow {

    protected $schoolId;

    // Constructor: Initializes schoolId and clears any previous import errors from the session
    public function __construct($schoolId) {
        $this->schoolId = $schoolId;
        Session::forget('import_errors'); // Clear previous import errors stored in the session
    }

    // Processes the collection of rows imported from the Excel file
    public function collection(Collection $rows) {
        // Retrieves the column mappings from the spreadsheet
        $columns = $this->getColumnMapping(); 
        $validatedRows = $rows->map(function ($row, $index) use ($columns) {
            return $this->processRow($row, $columns, $index); // Process each row
        });
        // Check and handle any duplicates based on RUT
        $this->handleDuplicates($validatedRows);
        // Process valid rows and insert them into the database
        $this->processValidRows($validatedRows);
    }

    // Defines the mapping between spreadsheet columns and database fields
    private function getColumnMapping() {
        return [
            'rut' => 'rut',
            'name' => 'nombre',
            'last_name' => 'apellido',
            'birth_date' => 'fecha_de_nacimiento',
            'address' => 'direccion',
            'commune' => 'comuna',
            'region' => 'region',
            'phone' => 'telefono',
            'num_load_family' => 'cargas_familiares',
            'marital_status' => 'estado_civil',
            'nationality' => 'nacionalidad',
            'worker_type' => 'tipo_de_trabajador',
            'function_worker' => 'funcion',
            'hire_date' => 'fecha_de_contrato',
            'termination_date' => 'fecha_de_termino',
            'worker_titular' => 'titular',
            'replacement_reason' => 'motivo_de_reemplazo',
            'contract_type' => 'tipo_de_contrato',
            'hourly_load' => 'carga_horaria',
            'carga_lunes' => 'lunes',
            'carga_martes' => 'martes',
            'carga_miercoles' => 'miercoles',
            'carga_jueves' => 'jueves',
            'carga_viernes' => 'viernes',
            'carga_sabado' => 'sabado',
            'unemployment_insurance' => 'seguro_de_cesantia',
            'retired' => 'jubilado',
            'service_start_year' => 'ano_inicio_servicio',
            'base_salary' => 'sueldo_base',
            'insurance_AFP' => 'afp',
            'insurance_ISAPRE' => 'salud',
            'apv' => 'apv',
            'cotization' => 'cotizacion_pactada',
            'unit' => 'unidad',
            'others_discounts' => 'descuentos',
            'city' => 'ciudad_de_registro',
            'origin_city' => 'ciudad_del_trabajador',
            'levels' => 'nivel',
            'schedule' => 'jornada',
        ];
    }

    // Processes each individual row by mapping its columns and validating it
    private function processRow($row, $columns, $index) {
        $row = $this->mapColumns($row, $columns);          // Maps the columns of the row to the required fields
        return $this->convertAndValidateRow($row, $index); // Validates the row and returns the processed row
    }

    // Maps data from the spreadsheet columns to the model attributes
    private function mapColumns($row, $columns) {
        $row['rut'] = $row[$columns['rut']];
        $row['name'] = Str::title($row[$columns['name']]);      // Capitalize the name
        $row['last_name'] = Str::title($row[$columns['last_name']]); // Capitalize the last name
        // Converts birth date using a custom service to handle the date format conversion
        $row['birth_date'] = Carbon::parse(ConvertDateService::handle($row[$columns['birth_date']]));
        $row['address'] = $row[$columns['address']];
        $row['commune'] = $row[$columns['commune']];
        $row['region'] = $row[$columns['region']];
        $row['phone'] = (string) $row[$columns['phone']];
        $row['num_load_family'] = $row[$columns['num_load_family']];
        // Maps marital status, worker type, and function worker based on predefined constants in the Worker model
        $row['marital_status'] = array_search($row[$columns['marital_status']], Worker::MARITAL_STATUS);
        $row['nationality'] = $row[$columns['nationality']];
        $row['worker_type'] = array_search($row[$columns['worker_type']], Worker::WORKER_TYPES);
        $row['function_worker'] = array_search($row[$columns['function_worker']], Worker::FUNCTION_WORKER);
        $row['hire_date'] = Carbon::parse($row[$columns['hire_date']]);
        $row['termination_date'] = Carbon::parse($row[$columns['termination_date']]);
        $row['replacement_reason'] = $row[$columns['replacement_reason']] ?? null;
        $row['contract_type'] = array_search($row[$columns['contract_type']], Contract::CONTRACT_TYPES);
        $row['hourly_load'] = $row[$columns['hourly_load']];
        // Assign hourly loads for each day of the week
        $this->assignHourlyLoads($row, $columns);
        // Assign insurance data for the worker
        $this->assignInsurancesWorker($row, $columns);

        // Convert unemployment insurance and retired status to boolean values
        $row['unemployment_insurance'] = (bool) $row[$columns['unemployment_insurance']];
        $row['retired'] = (bool) $row[$columns['retired']];

        // Handle service start year and base salary (ensure they are in the correct format)
        $row['service_start_year'] = $row[$columns['service_start_year']] ? (int) $row[$columns['service_start_year']] : null;
        $row['base_salary'] = $row[$columns['base_salary']] ? (float) $row[$columns['base_salary']] : null;
        $row['school_id'] = $this->schoolId;
        // Assign the worker's titular (supervisor)
        $this->assignWorkerTitular($row, $columns); 
        // Assign contract-related data
        $this->assingDataContract($row, $columns);  

        return $row;
    }

    // Assigns the hourly load values for each day of the week, defaulting to 0 if no value is found
    private function assignHourlyLoads(&$row, $columns) {
        foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'] as $day) {
            $row["carga_{$day}"] = isset($row[$columns["carga_{$day}"]]) ? $row[$columns["carga_{$day}"]] : 0;
        }
    }

    // Assigns the worker's titular (supervisor) based on the name in the spreadsheet
    private function assignWorkerTitular(&$row, $columns) {
        if (!empty($row[$columns['worker_titular']])) {
            $worker_titular = Worker::where('name', $row[$columns['worker_titular']])
                    ->where('school_id', $this->schoolId)
                    ->first();
            $row['worker_titular'] = $worker_titular ? $worker_titular->id : null;
        }
    }

    // Assigns contract-related data to the worker based on the row data from the spreadsheet
    private function assingDataContract(&$row, $columns) {
        if (!empty($row[$columns['city']])) {
            $row['city'] = $row[$columns['city']];
            $row['origin_city'] = $row[$columns['origin_city']];
            $row['levels'] = $row[$columns['levels']];
            $row['schedule'] = $row[$columns['schedule']];
        }
    }

    // Assigns the insurance information (AFP and ISAPRE) based on the provided data
    private function assignInsurancesWorker(&$row, $columns) {
        $this->assignInsurance($row, $columns['insurance_AFP'], 'insurance_AFP');
        $this->assignInsurance($row, $columns['insurance_ISAPRE'], 'insurance_ISAPRE');

        $row['apv'] = $row[$columns['apv']];
        $row['cotization'] = $row[$columns['cotization']];
        $row['unit'] = $row[$columns['unit']] ;
        $row['others_discounts'] = $row[$columns['others_discounts']] ;
    }

    // Assigns an individual insurance (either AFP or ISAPRE) by searching for it in the Insurance model
    private function assignInsurance(&$row, $insuranceColumn, $insuranceKey) {
        if (!empty($row[$insuranceColumn])) {
            $insurance = Insurance::where('name', $row[$insuranceColumn])->first();
            $row[$insuranceKey] = $insurance ? $insurance->id : null;
        }
    }

    // Validates a row using the WorkerFormRequest rules and attaches validation errors if any
    private function convertAndValidateRow($row, $index) {
        $validator = Validator::make($row->toArray(), (new WorkerFormRequest())->rules());
        if ($validator->fails()) {
            // Prefix the error message with the row number to identify where the error occurred
            $prefix = 'Fila ' . ($index + 1) . ': ';
            $row['errors'] = array_map(function ($value) use ($prefix) {
                return $prefix . ' ' . $value;
            }, $validator->errors()->all());
        }
        return $row;
    }

    // Handles duplicate rows by checking if any rows have the same RUT
    private function handleDuplicates($validatedRows) {
        // Get unique rows based on the worker's RUT
        $uniqueRows = $validatedRows->unique(function ($row) {
            return $row['rut'];
        });
        // Find the duplicate rows (those that were excluded in the unique set)
        $duplicateRows = $validatedRows->diff($uniqueRows);
        // If duplicates exist, add an error to the session and stop further processing
        if (!$duplicateRows->isEmpty()) {
            Session::forget('import_errors');
            $errorMessage = 'Existen filas con el mismo RUT en el archivo de importación.'; // Error message for duplicates
            Session::push('import_errors', $errorMessage);
            return;
        }
    }

    // Processes and inserts the valid rows into the database
    private function processValidRows($validatedRows) {
        foreach ($validatedRows as $row) {
            if (isset($row['errors'])) {
                Session::push('import_errors', $row['errors']);
                continue;
            }
            // Create or update the worker record, and insert related data such as contract, hourly load, and parameters
            $worker = Worker::createOrUpdateWorker($row->toArray());
            Contract::createOrUpdateContract($worker->id, new Request($row->toArray()));
            $worker->createHourlyLoadArray(new Request($row->toArray()));
            Parameter::insertParameters($worker->id, new Request($row->toArray()), $this->schoolId);
            // Process insurance data for the worker
            $insuranceMessage = $this->setInsurancesWorker($row, $worker);
            $this->updateDataContract($row, $worker);
            // If there are any insurance-related issues, add them to the session errors
            if (!empty($insuranceMessage)) {
                Session::push('import_errors', "El trabajador con RUT " . $row['rut'] . " ha excedido el límite de cotización. Solo se insertaron los datos del seguro.");
            }
        }
    }

    // Sets insurance data for the worker based on the AFP and ISAPRE columns in the row
    private function setInsurancesWorker(&$row, Worker $worker) {
        $Message = "";
        if (!empty($row['insurance_AFP']) || !empty($row['insurance_ISAPRE'])) {
            $insuranceAFP = $row['insurance_AFP'];
            $insuranceISAPRE = $row['insurance_ISAPRE'];
            // Update or insert both AFP and ISAPRE parameters for the worker
            $Message = Parameter::updateOrInsertBothInsuranceParams($worker->id, $this->schoolId, $insuranceAFP, $insuranceISAPRE, $row->ToArray());
        }
        return $Message;
    }

    // Updates the contract data for the worker, including details such as remuneration and schedule
    private function updateDataContract(&$row, Worker $worker) {
        if (!empty($row['city'])) {
            $contract = Contract::getContract($worker->id);
            $remuneration = $this->generateRemuneration($worker->worker_type, $row);
            $details = [
                'city' => $row['city'],
                'levels' => $row['levels'],
                'duration' => Contract::CONTRACT_TYPES[$row['contract_type']], // Take the corresponding contract type text
                'total_remuneration' => $remuneration,
                'remuneration_gloss' => ConvertNumberToWords::convert($remuneration),
                'origin_city' => $row['origin_city'],
                'schedule' => $row['schedule'],
                'teaching_hours' => $worker->worker_type == Worker::WORKER_TYPE_TEACHER ?? $row['hourly_load'] ,
                'curricular_hours' => $worker->worker_type == Worker::WORKER_TYPE_NON_TEACHER ?? $row['hourly_load'],
            ];
            $contract->details = json_encode($details);
            $contract->save();
        }
    }

    private function generateRemuneration($type, $row) {
        $rbnmTuituion = Tuition::where('title', "Valor RBMN")->value('tuition_id');

        return $type != Worker::WORKER_TYPE_TEACHER ? $row['base_salary'] : Parameter::getParameterValue($rbnmTuituion, 0, $this->schoolId) * $row['hourly_load'];
    }

}
