<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'month',
        'year',
        'details',
    ];

    public static function generatePayroll($schoolId, $month, $year)
    {
        // Obtener todos los trabajadores de la escuela
        $workers = Worker::where('school_id', $schoolId)->get();
        // Pre-cargar los IDs de los "tuition" para evitar repetir las consultas
        $tuitionTitles = [
            'RBMN','Desempeño dificil', 'Ley 19410', 'Ley 19464', 'Ley 19933', 'UMP',
            'Asignacion Voluntaria', 'Colegio de profesores', 'Prestamo Social Caja los Andes',
            'Prestamo Social Caja los heroes', 'Fundacion Lopez Perez',
        ];

        $tuitionIds = array_map(function ($title) use ($schoolId) {
            return Tuition::where('title', $title)->where('school_id', $schoolId)->value('tuition_id');
        }, $tuitionTitles);

        // Función para obtener el valor de un detalle
        $getDetailValue = function ($liquidation, $tuitionId, $key) {
            $value = $liquidation->getDetailByTuitionId($liquidation->id, $tuitionId, $key);
            return floatval(str_replace(',', '', $value));
        };

        // Procesar cada trabajador
        $payrollDetails = [];
        foreach ($workers as $worker) {
            // Obtener liquidaciones para el mes y año (si existe)
            $liquidations = Liquidation::where('worker_id', $worker->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            // Iniciar los datos del trabajador
            $totalsWorker = [
                'name' => $worker->name,
                'rut' => $worker->rut,
                'daysWorker' => 0,
                'monthlySalary' => 0,
                'hardPerformance' => 0,
                'law19410' => 0,
                'law19464' => 0,
                'law19933' => 0,
                'ump' => 0,
                'taxableIncome' => 0,
                'familyAllowance' => 0,
                'voluntaryAllowance' => 0,
                'totalEarnings' => 0,
                'afpContribution' => 0,
                'apv' => 0,
                'healthContribution' => 0,
                'unemploymentInsurance' => 0,
                'healthSupplement' => 0,
                'incomeTax' => 0,
                'professorCollege' => 0,
                'presLosAndes' => 0,
                'presLosHeroes' => 0,
                'fundationLp' => 0,
                'voluntaryDiscount' => 0,
                'totalPayable' => 0,
                'afpPercentage' => 0,
                'healthPercentage' => 0,
                'afpName' => '',
                'healthName' => '',
            ];

            if ($liquidations) {
                // Asignar los valores de la liquidación al trabajador
                $totalsWorker['daysWorker'] = $getDetailValue($liquidations, 'DIASTRABAJADOS', 'value');
                $totalsWorker['monthlySalary'] = $getDetailValue($liquidations, $tuitionIds[0], 'value') + $getDetailValue($liquidations, 'SUELDOBASE', 'value');
                // Asignar los valores de cada "tuition" usando los ids precargados
                $totalsWorker['hardPerformance'] = $getDetailValue($liquidations, $tuitionIds[1], 'value');
                $totalsWorker['law19410'] = $getDetailValue($liquidations, $tuitionIds[2], 'value');
                $totalsWorker['law19464'] = $getDetailValue($liquidations, $tuitionIds[3], 'value');
                $totalsWorker['law19933'] = $getDetailValue($liquidations, $tuitionIds[4], 'value');
                $totalsWorker['ump'] = $getDetailValue($liquidations, $tuitionIds[5], 'value');
                $totalsWorker['taxableIncome'] = $getDetailValue($liquidations, 'RENTAIMPONIBLE', 'value');
                $totalsWorker['familyAllowance'] = $getDetailValue($liquidations, 'ASIGNACIONFAMILIAR', 'value');
                $totalsWorker['voluntaryAllowance'] = $getDetailValue($liquidations, $tuitionIds[6], 'value');
                $totalsWorker['totalEarnings'] = $getDetailValue($liquidations, 'TOTALHABERES', 'value');
                $totalsWorker['afpContribution'] = $getDetailValue($liquidations, 'AFP', 'value');
                $totalsWorker['apv'] = $getDetailValue($liquidations, 'APV', 'value');
                $totalsWorker['healthContribution'] = $getDetailValue($liquidations, 'SALUD', 'value');
                $totalsWorker['unemploymentInsurance'] = $getDetailValue($liquidations, 'SEGUROCESANTIA', 'value');
                $totalsWorker['healthSupplement'] = $getDetailValue($liquidations, 'ADICIONALSALUD', 'value');
                $totalsWorker['incomeTax'] = $getDetailValue($liquidations, 'IMPUESTORENTA', 'value');
                $totalsWorker['professorCollege'] = $getDetailValue($liquidations, $tuitionIds[7], 'value');
                $totalsWorker['presLosAndes'] = $getDetailValue($liquidations, $tuitionIds[8], 'value');
                $totalsWorker['presLosHeroes'] = $getDetailValue($liquidations, $tuitionIds[9], 'value');
                $totalsWorker['fundationLp'] = $getDetailValue($liquidations, $tuitionIds[10], 'value');
                $totalsWorker['voluntaryDiscount'] = $getDetailValue($liquidations, 'DESCUENTOSVOLUNTARIOS', 'value') +
                    $getDetailValue($liquidations, 'IMPUESTORENTA', 'value') +
                    $getDetailValue($liquidations, 'DESCUENTOSLEGALES', 'value');
                $totalsWorker['totalPayable'] = $getDetailValue($liquidations, 'TOTALAPAGAR', 'value');

                // Obtener los títulos de AFP y Salud
                $afpTitle = $liquidations->getDetailByTuitionId($liquidations->id, 'AFP', 'title');
                $healthTitle = $liquidations->getDetailByTuitionId($liquidations->id, 'SALUD', 'title');

                // Procesar el título de AFP
                $poscierre = strpos($afpTitle, ")");
                $afpName = trim(substr($afpTitle, $poscierre + 1));
                $porc = trim(substr($afpTitle, 1, $poscierre - 1));
                $afpPercentageString = str_replace("%", "", $porc);

                $totalsWorker['afpName'] = $afpName;
                $totalsWorker['afpPercentage'] = $afpPercentageString;

                // Procesar el titulo de SALUD
                $poscierre = strpos($healthTitle, ")");
                $titleHeath = trim(substr($healthTitle, $poscierre + 1));

                // Procesar el título de Salud
                $totalsWorker['healthName'] = $titleHeath;
            }
            // Guardamos los detalles de este trabajador
            $payrollDetails[] = $totalsWorker;
        }

        // Crear o actualizar la planilla
        $payroll = self::updateOrCreate(
            [
                'school_id' => $schoolId,
                'month' => $month,
                'year' => $year,
            ], [
                'details' => json_encode($payrollDetails), // Guardar detalles de cada trabajadorF
            ]
        );

        return $payroll;
    }

    // Definir la relación con el modelo 'School' (relación de uno a muchos)
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
