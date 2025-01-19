<?php

namespace App\Imports;

use App\Http\Requests\WorkerFormRequest; 
use App\Models\Worker;
use App\Models\Contract;
use App\Models\Parameter;
use App\Services\ConvertDateService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WorkersImport implements ToCollection, WithHeadingRow
{
    protected $schoolId;

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
        Session::forget('import_errors');
    }

    public function collection(Collection $rows)
    {
        $columns = [
            'rut' => 'RUT',
            'name' => 'Nombre',
            'last_name' => 'Apellido',
            'birth_date' => 'Fecha de nacimiento',
            'address' => 'Dirección',
            'commune' => 'Comuna',
            'region' => 'Región',
            'phone' => 'Teléfono',
            'marital_status' => 'Estado civil',
            'nationality' => 'Nacionalidad',
            'worker_type' => 'Tipo de trabajador', 
            'function_worker' => 'Función', 
            'hire_date' => 'Fecha de contrato',
            'termination_date' => 'Fecha de término',
            'worker_titular' => 'Titular',
            'hourly_load' => 'Carga Horaria',
            'carga_lunes' => 'Lunes', 
            'carga_martes' => 'Martes', 
            'carga_miercoles' => 'Miércoles', 
            'carga_jueves' => 'Jueves', 
            'carga_viernes' => 'Viernes', 
            'carga_sabado' => 'Sábado', 
            'unemployment_insurance' => 'Seguro de cesantía', 
            'retired' => 'Jubilado', 
            'service_start_year' => 'Año inicio servicio',
            'base_salary' => 'Sueldo base',
        ];
    
        $validatedRows = $rows->map(function ($row, $index) use ($columns) {
            // Convertir los valores de las columnas según el mapeo y reglas definidas
            $row['rut'] = preg_replace("/[^\d]/", '', $row[$columns['rut']]); // Limpiar RUT de caracteres no numéricos
            $row['name'] = Str::title($row[$columns['name']]);
            $row['last_name'] = Str::title($row[$columns['last_name']]);
            $row['birth_date'] = Carbon::parse(app(ConvertDateService::class)->handle($row[$columns['birth_date']]));
            $row['address'] = $row[$columns['address']];
            $row['commune'] = array_flip(config('communes_region.COMMUNE_OPTIONS'))[Str::title($row[$columns['commune']])] ?? null;
            $row['region'] = array_flip(config('communes_region.REGIONES_OPTIONS'))[Str::title($row[$columns['region']])] ?? null;            
            $row['phone'] = (string) $row[$columns['phone']];
            // Convertir 'Estado civil' a índice
            $row['marital_status'] = array_search(Str::upper($row[$columns['marital_status']]), Worker::MARITAL_STATUS) ;
            // Convertir 'Tipo de trabajador' a índice
            $row['worker_type'] = array_search(Str::title($row[$columns['worker_type']]), Worker::WORKER_TYPES) ;
            // Convertir 'Función' a índice
            $row['function_worker'] = array_search(Str::title($row[$columns['function_worker']]), Worker::FUNCTION_WORKER) ;
            // Fecha de inicio del contrato
            $row['hire_date'] = Carbon::parse($row[$columns['hire_date']]);
            $row['termination_date'] = isset($row[$columns['termination_date']]) ?? Carbon::parse($row[$columns['termination_date']]) ;
            $row['worker_titular'] = Worker::where('name', $row[$columns['worker_titular']])->where('school_id', $this->schoolId)->first()->id;
            // Procesar carga horaria para cada día de la semana
            $row['hourly_load'] = isset($row[$columns['hourly_load']]) ?? (float) $row[$columns['hourly_load']];
            $row['carga_lunes'] = isset($row[$columns['carga_lunes']]) ? (float) $row[$columns['carga_lunes']] : 0;
            $row['carga_martes'] = isset($row[$columns['carga_martes']]) ? (float) $row[$columns['carga_martes']] : 0;
            $row['carga_miercoles'] = isset($row[$columns['carga_miercoles']]) ? (float) $row[$columns['carga_miercoles']] : 0;
            $row['carga_jueves'] = isset($row[$columns['carga_jueves']]) ? (float) $row[$columns['carga_jueves']] : 0;
            $row['carga_viernes'] = isset($row[$columns['carga_viernes']]) ? (float) $row[$columns['carga_viernes']] : 0;
            $row['carga_sabado'] = isset($row[$columns['carga_sabado']]) ? (float) $row[$columns['carga_sabado']] : 0;
            // Convertir booleanos
            $row['unemployment_insurance'] = (bool) $row[$columns['unemployment_insurance']];
            $row['retired'] = (bool) $row[$columns['retired']];
            // Año de inicio servicio
            $row['service_start_year'] = isset($row[$columns['service_start_year']]) ? (int) $row[$columns['service_start_year']] : null;
            // Salario base
            $row['base_salary'] = isset($row[$columns['base_salary']]) ? (float) $row[$columns['base_salary']] : null;
            // Validar los datos según las reglas del WorkerFormRequest
            $validator = Validator::make($row, (new WorkerFormRequest())->rules());
    
            if ($validator->fails()) {
                $prefix = 'Fila '.($index + 2).': ';
                $row['errors'] = array_map(function ($value) use ($prefix) {
                    return $prefix.' '.$value;
                }, $validator->errors()->all());
            }
    
            return $row;
        });
    
        foreach ($validatedRows as $row) {
            // Si hay errores, no se crea el trabajador y se muestra el error
            if (isset($row['errors'])) {
                Session::push('import_errors', $row['errors']);
                continue;
            }
            // Crear o actualizar el trabajador
            $worker = Worker::createOrUpdateWorker($row);
            // Actualizar el contrato
            Contract::createOrUpdateContract($worker->id, $row);
            // Crear el arreglo de carga horaria
            $hourlyLoadArray = Worker::createHourlyLoadArray($row);
            // Actualizar la carga horaria del trabajador
            $worker->updateHourlyLoad($hourlyLoadArray);
            // Insertar parámetros
            Parameter::insertParameters($worker->id, $row, $this->schoolId);
        }
    }
    
}