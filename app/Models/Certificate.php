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
    /**
     * Retrieves certificates for a specific school and distinct years.
     * 
     * This method fetches a list of distinct years in which certificates were issued for a specific school.
     * 
     * @param int $school_id The ID of the school to filter by
     * @return \Illuminate\Database\Eloquent\Collection A collection of distinct years in which certificates were issued
     */
    public static function getCertificateForSchool($school_id)
    {
        // Fetch distinct years from the 'Certificate' model where the school ID matches
        return self::where('school_id', $school_id)
            ->select('year') // Only select the 'year' column
            ->distinct() // Avoid duplicate entries
            ->orderByDesc('year') // Order by year in descending order
            ->get(); // Retrieve the result
    }
    /**
     * Creates or updates a certificate for a specific worker, school, and year.
     * 
     * This method generates a certificate for a worker for the given year at a specific school. 
     * It collects various financial details such as income, deductions, and salary, calculates adjusted 
     * salary values, and stores the certificate data.
     * 
     * @param int $school_id The ID of the school
     * @param int $worker_id The ID of the worker
     * @param int $year The year for which the certificate is generated
     * @return void
     */
    public static function createCertificate($school_id, $worker_id, $year)
    {
        // Fetch school and sustainer details using the school ID
        $school = School::find($school_id);
        $sustainer = $school->sustainer;
        if (!$sustainer) {
            return; // Return if no sustainer found
        }
        // Fetch the worker details
        $worker = Worker::find($worker_id);
        $workerInfo = [
            'worker_name' => $worker->name,
            'worker_rut' => $worker->rut,
            'year' => $year,
        ];
        // Fetch sustainer details
        $sustainerInfo = [
            'sustainer_name' => $sustainer->business_name,
            'sustainer_rut' => $sustainer->rut,
            'sustainer_address' => $sustainer->address,
            'sustainer_legal_nature' => $sustainer->legal_nature,
        ];
        // Fetch monetary configuration data
        $monetaryData = config('monetarycom.datos');
        // If the given year is not available in the configuration, use the previous year
        if (!isset($monetaryData[$year])) {
            $year = (int) $year - 1;
        }
        // Initialize arrays for monthly data and totals
        $data = [];
        $totals = [
            'income_total' => 0,
            'deductions_total' => 0,
            'taxable_salary_total' => 0,
            'tax_amount_total' => 0,
            'adjusted_salary_total' => 0,
            'adjusted_tax_total' => 0,
        ];
        // Loop through each month (0 to 11) to gather monthly data for the worker
        for ($i = 0; $i <= 11; $i++) {
            $liquidation = Liquidation::where('worker_id', $worker->id)
                ->where('month', $i + 1) // Month (1-12)
                ->where('year', $year)
                ->first();
            // If no liquidation exists for the month, set all values to 0
            if (!$liquidation) {
                $income = 0;
                $legal_deductions = 0;
                $taxable_salary = 0;
                $tax_amount = 0;
                $adjusted_salary = 0;
                $adjusted_tax = 0;
            } else {
                // If liquidation exists, fetch detailed values
                $income = Liquidation::getDetailByTuitionId($liquidation->id, "RENTAIMPONIBLE", 'value');
                $legal_deductions = Liquidation::getDetailByTuitionId($liquidation->id, "DESCUENTOSLEGALES", 'value');
                $taxable_salary = Liquidation::getDetailByTuitionId($liquidation->id, "REMUNERACIONTRIBUTABLE", 'value');
                $tax_amount = Liquidation::getDetailByTuitionId($liquidation->id, "IMPUESTORENTA", 'value');
                // Get capital value from the monetary data configuration
                $month_values = $monetaryData[$year][$i];
                $capital_value = null;
                foreach ($month_values as $value) {
                    if ($value !== null) {
                        $capital_value = $value;
                        break;
                    }
                }
                // Calculate adjusted salary and adjusted tax by multiplying with capital value
                $adjusted_salary = (double) $taxable_salary * $capital_value;
                $adjusted_tax = (double) $tax_amount * $capital_value;
                // Format the adjusted values for display
                $adjusted_salary = number_format($adjusted_salary, 0, 0, ",");
                $adjusted_tax = number_format($adjusted_tax, 0, 0, ",");
            }
            // Accumulate totals for all months
            $totals['income_total'] += str_replace(",", "", $income);
            $totals['deductions_total'] += str_replace(",", "", $legal_deductions);
            $totals['taxable_salary_total'] += str_replace(",", "", $taxable_salary);
            $totals['tax_amount_total'] += str_replace(",", "", $tax_amount);
            $totals['adjusted_salary_total'] += str_replace(",", "", $adjusted_salary);
            $totals['adjusted_tax_total'] += str_replace(",", "", $adjusted_tax);
            // Store monthly data
            $data[] = [
                'month' => MonthHelper::integerToMonth($i + 1), // Convert month number to name
                'income' => $income,
                'legal_deductions' => $legal_deductions,
                'taxable_salary' => $taxable_salary,
                'tax_amount' => $tax_amount,
                'adjusted_salary' => $adjusted_salary,
                'adjusted_tax' => $adjusted_tax,
            ];
        }
        // Check if a certificate already exists for the worker for the given year
        $existingCertificate = self::where('worker_id', $worker_id)
            ->where('year', $year)
            ->where('school_id', $school_id)
            ->first();
        // We evaluate whether there are previous registered certificates
        if ($existingCertificate) {
            // Update the description if certificate already exists
            $existingCertificate->description = json_encode([
                'sustainer_info' => $sustainerInfo,
                'worker_info' => $workerInfo,
                'monthly_data' => $data,
                'totals' => $totals,
            ]);
            $existingCertificate->save();
        } else {
            // Create a new certificate if it doesn't exist
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

            self::create($certificateData); // Create the certificate
        }
    }
    /**
     * Retrieves certificates for a specific year and school, and formats them for display.
     * 
     * This method fetches all workers for the given school and year, along with their certificates, 
     * and formats them for easy use in views.
     * 
     * @param int $year The year of the certificates to retrieve
     * @param int $school_id The school ID to filter the certificates by
     * @return \Illuminate\Support\Collection A collection of formatted certificate data
     */
    public static function getCertificates($year, $school_id)
    {
        // Fetch workers, their certificates for the specified year, and the sustainer data
        $workersData = Worker::where('school_id', $school_id)
            ->with(['certificates' => function ($query) use ($year) {
                $query->where('year', $year); // Filter certificates by year
            }, 'school.sustainer'])->get();

        // Format the data for the view and filter out any null entries (workers without certificates)
        return $workersData->map(function ($worker) use ($year) {
            $certificate = $worker->certificates->first(); // Only get the first certificate for the year

            if (!$certificate) {
                return null; // Return null if no certificate exists for the worker
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
        })->filter(); // Filter out null values (workers without certificates)
    }
    /**
     * Relationship: A Certificate belongs to a Worker.
     * 
     * This defines the relationship where a certificate is linked to a worker. We can access the worker
     * associated with a certificate using this method.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship between Certificate and Worker
     */
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
    /**
     * Relationship: A Certificate belongs to a School.
     * 
     * This defines the relationship where a certificate is linked to a school. We can access the school
     * associated with a certificate using this method.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship between Certificate and School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
