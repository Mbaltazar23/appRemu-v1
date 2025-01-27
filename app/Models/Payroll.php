<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model {

    use HasFactory;

    // Attributes that can be mass-assigned
    protected $fillable = [
        'school_id', // ID of the school
        'month', // Month of the payroll
        'year', // Year of the payroll
        'details', // Details of the payroll (usually a JSON)
    ];

    /**
     * Generates the payroll for a specific month and year in a school.
     * 
     * @param int $schoolId - ID of the school.
     * @param int $month - Month of the payroll.
     * @param int $year - Year of the payroll.
     * @return \App\Models\Payroll - Generated or updated payroll object.
     */
    public static function generatePayroll($schoolId, $month, $year) {
        // Get all workers of the school
        $workers = Worker::where('school_id', $schoolId)->get();
        // Titles of different types of allowances or deductions (e.g., law 19410, difficult performance, etc.)
        $tuitionTitles = [
            'RBMN', 'Desempeño dificil', 'Ley 19410', 'Ley 19464', 'Ley 19933', 'UMP',
            'Asignacion Voluntaria', 'Colegio de profesores', 'Prestamo Social Caja los Andes',
            'Prestamo Social Caja los heroes', 'Fundacion Lopez Perez',
        ];
        // Get the tuition IDs for the specific school
        $tuitionIds = array_map(function ($title) use ($schoolId) {
            return Tuition::where('title', $title)->where('school_id', $schoolId)->value('tuition_id');
        }, $tuitionTitles);
        // Function to get the value of a detail
        $getDetailValue = function ($liquidation, $tuitionId, $key) {
            $value = $liquidation->getDetailByTuitionId($liquidation->id, $tuitionId, $key);
            return floatval(str_replace(',', '', $value));
        };
        // Process each worker
        $payrollDetails = [];
        foreach ($workers as $worker) {
            // Get liquidations for the specific month and year (if any)
            $liquidations = Liquidation::where('worker_id', $worker->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

            // Initialize worker's data
            $totalsWorker = [
                'name' => $worker->name,
                'rut' => $worker->rut,
                'daysWorker' => 0,
                'absentDays' => 0,
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
                // Assign liquidation values to the worker
                $totalsWorker['daysWorker'] = $getDetailValue($liquidations, 'DIASTRABAJADOS', 'value');
                $totalsWorker['absentDays'] = $getDetailValue($liquidations, 'DIASNOTRABAJADOS', 'value');
                $totalsWorker['monthlySalary'] = $getDetailValue($liquidations, $tuitionIds[0], 'value') + $getDetailValue($liquidations, 'SUELDOBASE', 'value');
                // Assign values for each "tuition" using the preloaded IDs
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
                // Get the titles for AFP and Health
                $afpTitle = $liquidations->getDetailByTuitionId($liquidations->id, 'AFP', 'title');
                $healthTitle = $liquidations->getDetailByTuitionId($liquidations->id, 'SALUD', 'title');
                // Process AFP title
                $poscierre = strpos($afpTitle, ")");
                $afpName = trim(substr($afpTitle, $poscierre + 1));
                $porc = trim(substr($afpTitle, 1, $poscierre - 1));
                $afpPercentageString = str_replace("%", "", $porc);

                $totalsWorker['afpName'] = $afpName;
                $totalsWorker['afpPercentage'] = $afpPercentageString;

                // Process Health title
                $poscierre = strpos($healthTitle, ")");
                $titleHeath = trim(substr($healthTitle, $poscierre + 1));

                // Process the Health title
                $totalsWorker['healthName'] = $titleHeath;
            }
            // Save worker details
            $payrollDetails[] = $totalsWorker;
        }

        // Create or update the payroll
        $payroll = self::updateOrCreate(
                        [
                    'school_id' => $schoolId,
                    'month' => $month,
                    'year' => $year,
                        ], [
                    'details' => json_encode($payrollDetails), // Save details of each worker
                        ]
        );

        return $payroll;
    }

    // Define the relationship with the 'School' model (one to many relationship)
    public function school() {
        return $this->belongsTo(School::class);
    }

}
