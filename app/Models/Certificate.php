<?php

namespace App\Models;

use App\Helpers\MonthHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    // Set the table columns you can mass-assign
    protected $fillable = [
        'worker_id',
        'school_id',
        'year',
        'description',
    ];

    public static function getCertificateForSchool($school_id)
    {
        return self::where('school_id', $school_id)
            ->select('year') // Solo seleccionar el año
            ->distinct() // Evitar duplicados
            ->orderByDesc('year') // Ordenar por el año de forma descendente
            ->get(); // Agrupar por el campo 'year'
    }
    public static function createCertificate($school_id, $worker_id, $year)
    {
        // Obtener la escuela
        $school = School::find($school_id);
    
        // Obtener el sostenedor de la escuela
        $sustainer = $school->sustainer;
        if (!$sustainer) {
            return; // Si no existe el sostenedor, retornamos sin hacer nada
        }
    
        // Obtener el trabajador
        $worker = Worker::find($worker_id);
    
        // Datos del trabajador
        $workerInfo = [
            'worker_name' => $worker->name,
            'worker_rut' => $worker->rut,
            'year' => $year,
        ];
    
        // Datos del sostenedor
        $sustainerInfo = [
            'sustainer_name' => $sustainer->business_name,
            'sustainer_rut' => $sustainer->rut,
            'sustainer_address' => $sustainer->address,
            'sustainer_legal_nature' => $sustainer->legal_nature,
        ];
    
        // Obtener los valores de la configuración
        $monetaryData = config('monetarycom.datos');
        
        // Verificar si el año solicitado existe en los datos de configuración
        if (!isset($monetaryData[$year])) {
            // Si el año no existe, tomamos el año anterior
            $year = (int) $year - 1;
        }
    
        // Inicializar un arreglo para almacenar los datos de los meses
        $data = [];
        $totals = [
            'income_total' => 0,
            'deductions_total' => 0,
            'taxable_salary_total' => 0,
            'tax_amount_total' => 0,
            'adjusted_salary_total' => 0,
            'adjusted_tax_total' => 0,
        ];
    
        // Recopilamos la información de cada mes
        for ($i = 0; $i <= 11; $i++) {
            // Obtener la liquidación para el trabajador y el mes correspondiente
            $liquidation = Liquidation::where('worker_id', $worker->id)
                ->where('month', $i + 1)  // Corregimos el índice para el mes
                ->where('year', $year)
                ->first();
    
            // Si no existe la liquidación para este mes, asignamos valores en 0
            if (!$liquidation) {
                $income = 0;
                $legal_deductions = 0;
                $taxable_salary = 0;
                $tax_amount = 0;
                $adjusted_salary = 0;
                $adjusted_tax = 0;
            } else {
                // Si existe la liquidación, obtenemos los valores correspondientes
                $income = Liquidation::getDetailByTuitionId($liquidation->id, "RENTAIMPONIBLE", 'value');
                $legal_deductions = Liquidation::getDetailByTuitionId($liquidation->id, "DESCUENTOSLEGALES", 'value');
                $taxable_salary = Liquidation::getDetailByTuitionId($liquidation->id, "REMUNERACIONTRIBUTABLE", 'value');
                $tax_amount = Liquidation::getDetailByTuitionId($liquidation->id, "IMPUESTORENTA", 'value');
                // Ahora, accedemos al primer valor del capital inicial para el mes correspondiente
                $capital_value = $monetaryData[$year]['capital_inicial'][$i];  // Acceder al valor flotante directamente
                // Multiplicación de valores con el capital inicial
                $adjusted_salary = (double) $taxable_salary * $capital_value; // Multiplicar REMUNERACIONTRIBUTABLE por el capital
                $adjusted_tax = (double) $tax_amount * $capital_value; // Multiplicar IMPUESTORENTA por el capital
                // Formatear los valores para mostrar en el certificado
                $adjusted_salary = number_format($adjusted_salary, 0, 0, ",");
                $adjusted_tax = number_format($adjusted_tax, 0, 0, ",");
            }
    
            // Almacenar los totales
            $totals['income_total'] += str_replace(",", "", $income);
            $totals['deductions_total'] += str_replace(",", "", $legal_deductions);
            $totals['taxable_salary_total'] += str_replace(",", "", $taxable_salary);
            $totals['tax_amount_total'] += str_replace(",", "", $tax_amount);
            $totals['adjusted_salary_total'] += str_replace(",", "", $adjusted_salary);
            $totals['adjusted_tax_total'] += str_replace(",", "", $adjusted_tax);
    
            // Almacenar los datos mensuales en el arreglo $data
            $data[] = [
                'month' => MonthHelper::integerToMonth($i + 1), // Convertir mes numérico a nombre
                'income' => $income,
                'legal_deductions' => $legal_deductions,
                'taxable_salary' => $taxable_salary,
                'tax_amount' => $tax_amount,
                'adjusted_salary' => $adjusted_salary,
                'adjusted_tax' => $adjusted_tax,
            ];
        }
    
        // Buscar si ya existe un certificado para el trabajador en ese año
        $existingCertificate = self::where('worker_id', $worker_id)
            ->where('year', $year)
            ->where('school_id', $school_id)
            ->first();
    
        if ($existingCertificate) {
            // Si existe el certificado, actualizamos su descripción
            $existingCertificate->description = json_encode([
                'sustainer_info' => $sustainerInfo,
                'worker_info' => $workerInfo,
                'monthly_data' => $data,
                'totals' => $totals,
            ]);
            $existingCertificate->save();
        } else {
            // Si no existe el certificado, lo creamos
            $certificateData = [
                'worker_id' => $worker_id,
                'year' => $year,
                'description' => json_encode([
                    'sustainer_info' => $sustainerInfo,
                    'worker_info' => $workerInfo,
                    'monthly_data' => $data,
                    'totals' => $totals,
                ]),
                'school_id' => $school_id,
            ];
    
            self::create($certificateData); // Crear un nuevo certificado
        }
    }
    
    

    public static function getCertificates($year, $school_id)
    {
        $workersData = Worker::where('school_id', $school_id)
            ->with(['certificates' => function ($query) use ($year) {
                // Filtramos los certificados por el año
                $query->where('year', $year);
            }, 'school.sustainer']) // Relación con el sostenedor de la escuela
            ->get();

        // Transformar los datos para el formato que se necesita en la vista
        return $workersData->map(function ($worker) use ($year) {
            $certificate = $worker->certificates->first(); // Solo tomamos el primer certificado para el año

            if (!$certificate) {
                return null; // Si no existe el certificado, retornamos null
            }

            $certificateData = json_decode($certificate->description, true);

            return [
                'sustainer_name' => $certificateData['sustainer_info']['sustainer_name'],
                'sustainer_rut' => $certificateData['sustainer_info']['sustainer_rut'],
                'sustainer_address' => $certificateData['sustainer_info']['sustainer_address'],
                'sustainer_legal_nature' => $certificateData['sustainer_info']['sustainer_legal_nature'],
                'worker_name' => $worker->name,
                'worker_rut' => $worker->rut,
                'year' => $year,
                'months_data' => $certificateData['monthly_data'],
                'total_values' => $certificateData['totals'],
            ];
        })->filter(); // Filtramos los datos nulos (trabajadores sin certificados)

    }

    // Define the relationship with the Worker model (assuming the relationship is defined)
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    // Relación con School
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
