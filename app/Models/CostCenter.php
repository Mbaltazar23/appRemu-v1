<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    use HasFactory;
    /**
     * Retrieves the options for the 'item' selector.
     * 
     * This method returns an array of possible values for the 'item' field, which
     * could represent different job categories (e.g., 'Directivos', 'Administrativos', 'Docentes').
     * 
     * @return array The available 'item' options
     */
    public static function getItemOptions()
    {
        return [
            'item_ds' => 'Directivos superiores', // Senior management
            'item_ad' => 'Administrativos',      // Administrative staff
            'item_do' => 'Docentes',             // Teachers
        ];
    }
    /**
     * Retrieves the options for the 'periodo' (period) selector.
     * 
     * This method returns an array of available options for the 'periodo' field, which could represent
     * months or periods like "First Semester" or "Annual".
     * 
     * @return array The available 'periodo' options
     */
    public static function getPeriodOptions()
    {
        return [
            'Enero' => 'Enero',
            'Febrero' => 'Febrero',
            'Marzo' => 'Marzo',
            'Abril' => 'Abril',
            'Mayo' => 'Mayo',
            'Junio' => 'Junio',
            'Julio' => 'Julio',
            'Agosto' => 'Agosto',
            'Septiembre' => 'Septiembre',
            'Octubre' => 'Octubre',
            'Noviembre' => 'Noviembre',
            'Diciembre' => 'Diciembre',
            '1erSemestre' => '1er Semestre',  // First semester
            '2doSemestre' => '2do Semestre',  // Second semester
            'Anual' => 'Año completo',        // Full year
        ];
    }
    /**
     * Retrieves the total sums of liquidations for a school, filtered by 'item' and 'periodo'.
     * 
     * This method calculates the total liquidations for a specific school based on a specific
     * 'item' (e.g., Administrative, Teaching, Senior Management) and a given 'periodo' (e.g., January, First Semester).
     * 
     * @param int $school_id The ID of the school
     * @param string $item The item category (e.g., 'item_ds', 'item_ad', 'item_do')
     * @param string $periodo The period (e.g., 'Enero', '1erSemestre')
     * @return array|bool An array of total sums or an error message if there are invalid inputs
     */
    public static function getLiquidationSumsTotalCosts($school_id, $item, $periodo)
    {
        // Get the filter conditions based on item and period
        $conditions = self::generateFilterConditions($item, $periodo);

        // If there is an error with the conditions (invalid item or period), return the error
        if (isset($conditions['error'])) {
            return $conditions['error']; // Return the error if item or period is invalid
        }

        $strfuncion = $conditions['strfuncion'];  // The worker's function condition
        $strmeses = $conditions['strmeses'];      // The period condition (month/semester)

        // Define the categories we want to sum (e.g., 'RENTAIMPONIBLE', 'DESCUENTOSLEGALES')
        $categories = [
            'RENTAIMPONIBLE', 'DESCUENTOSLEGALES', 'IMPUESTORENTA', 'DESCUENTOSVOLUNTARIOS',
            'TOTALAPAGAR', 'AFP', 'SALUD', 'SEGUROCESANTIA', 'LICENCIA', 'INASISTENCIA',
        ];

        // Get the workers in the specified school and apply the function filter
        $workers = Worker::where('school_id', $school_id)
            ->where(function ($query) use ($strfuncion) {
                $query->whereRaw($strfuncion); // Apply the function filter for the worker
            })
            ->get(); // Retrieve the workers that match the conditions

        // If no workers are found, return an empty array
        if ($workers->count() < 0) {
            return [
                'workers' => [], // Return an empty array if no workers match
            ];
        }
        // Iterate through the workers and calculate their liquidation totals
        foreach ($workers as $worker) {
            // Initialize an array to store the totals for each category for this worker
            $workerTotals = array_fill_keys($categories, 0);

            foreach ($categories as $category) {
                // Get the sum of liquidations for the worker for each category
                $workerTotals[$category] = self::getSumLiquidationsByWorkerAndMonth(
                    $worker->id, $category, $strfuncion, $strmeses
                );
            }
            // Optionally, we store the totals in the worker object if needed
            $worker->totals = $workerTotals;
        }
        // Return the workers with their respective liquidation totals
        return [
            'workers' => $workers,
        ];
    }
    /**
     * Retrieves the total liquidation sum for a specific worker and category (tuition_id) within a given period (month).
     * 
     * This method calculates the sum of liquidation values for a specific worker, filtered by category (tuition_id)
     * and month (e.g., "month=1" for January). It also considers the worker's function (e.g., teacher, administrator).
     * 
     * @param int $workerId The ID of the worker
     * @param string $tuitionId The tuition category ID within liquidation details
     * @param string $strfunction The worker's function (e.g., "Docente de aula")
     * @param string $strmonth The month condition (e.g., "month=1" for January)
     * @return float The total sum of liquidations for this worker, category, and period
     */
    public static function getSumLiquidationsByWorkerAndMonth($workerId, $tuitionId, $strfunction, $strmonth)
    {
        // Retrieve liquidations for the worker filtered by function and month
        $liquidations = Liquidation::where('worker_id', $workerId)
            ->whereRaw($strmonth)  // Apply the month filter
            ->whereHas('worker', function ($query) use ($strfunction) {
                $query->whereRaw($strfunction); // Apply the worker function filter
            })
            ->get();
        // If there are no liquidations, return 0
        if ($liquidations->isEmpty()) {
            return 0;
        }
        // Initialize a variable to accumulate the sum of values
        $totalSum = 0;
        // Loop through the liquidations and sum the corresponding values from details
        foreach ($liquidations as $liquidation) {
            // Access the liquidation details (stored as JSON)
            $details = json_decode($liquidation->details, true); // 'true' convierte a un array asociativo
            // Loop through the details to find the tuition_id and sum its values
            foreach ($details as $detail) {
                // If the tuition_id matches, sum the 'value' field
                if (isset($detail['tuition_id']) && $detail['tuition_id'] == $tuitionId) {
                    // Clean up the value (remove commas if present) and add it to the total sum
                    if (isset($detail['value'])) {
                        $totalSum += (float) str_replace(',', '', $detail['value']);
                    }
                }
            }
        }
        // Return the total sum (0 if no values found)
        return $totalSum;
    }
    /**
     * Generates filter conditions based on 'item' and 'periodo'.
     * 
     * This method creates filter conditions that can be used to filter workers and liquidations
     * based on the selected 'item' (e.g., job category) and 'periodo' (e.g., month or semester).
     * 
     * @param string $item The selected item (e.g., 'item_ds', 'item_ad', 'item_do')
     * @param string $periodo The selected period (e.g., 'Enero', '1erSemestre')
     * @return array The filter conditions or an error message if invalid
     */
    public static function generateFilterConditions($item, $periodo)
    {
        // Define the function filters for each item type
        $funciones = [
            'item_ds' => "function_worker=5 OR function_worker=6 OR function_worker=7", // Senior Management
            'item_ad' => "function_worker=3 OR function_worker=8 OR function_worker=9", // Administrative
            'item_do' => "function_worker=1", // Teachers
        ];
        // Define the month filters for each period
        $meses = [
            'Enero' => "month=1",
            'Febrero' => "month=2",
            'Marzo' => "month=3",
            'Abril' => "month=4",
            'Mayo' => "month=5",
            'Junio' => "month=6", 
            'Julio' => "month=7",
            'Agosto' => "month=8",
            'Septiembre' => "month=9",
            'Octubre' => "month=10",
            'Noviembre' => "month=11",
            'Diciembre' => "month=12",
            '1erSemestre' => "month<7",  // First Semester
            '2doSemestre' => "month>6",   // Second Semester
            'Anual' => "month>0",         // Full Year
        ];
        // Validate the 'item' and 'periodo' inputs
        if (!isset($funciones[$item]) || !isset($meses[$periodo])) {
            return [
                'error' => 'Datos del item o periodo incorrectos.', // Error if invalid data
            ];
        }
        // return result
        return [
            'strfuncion' => $funciones[$item],  // The worker's function condition
            'strmeses' => $meses[$periodo],     // The month/semester condition
            'tititem' => ucfirst(str_replace('item_', '', $item)), // Friendly name for item
            'titperiodo' => $periodo,           // Friendly name for period
        ];
    }
}
