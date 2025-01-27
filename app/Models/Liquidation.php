<?php

namespace App\Models;

use App\Helpers\LiquidationHelper;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liquidation extends Model {

    use HasFactory;

    // Fields that can be mass-assigned
    protected $fillable = [
        'worker_id',
        'month',
        'year',
        'values',
        'details', // Added 'details' to be mass-assignable
        'glosa', // Added 'glosa' for mass-assignment if you're going to assign it directly
    ];

    /**
     * Store a new liquidation or update an existing one.
     *
     * This method creates a new liquidation for a worker for the given month and year.
     * If a liquidation already exists for the specified period, it will be deleted first
     * and a new one will be created.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $workerId
     * @return \App\Models\Liquidation
     */
    public static function storeLiquidation($request, $workerId) {
        $month = $request["month"];
        $year = $request['year'];
        // First, delete any existing liquidation for the same worker, month, and year
        self::deleteLiquidation($month, $year, $workerId);
        // Create a new liquidation record
        $liquidation = self::create([
                    'worker_id' => $workerId,
                    'month' => $month,
                    'year' => $year,
        ]);
        // Initialize an array to store liquidation details
        $details = [];
        $clases = Tuition::where('school_id', $request->school_id)->get();
        // Loop through each class and add details if they exist in the request
        foreach ($clases as $clase) {
            $idval = 'VALID' . $clase->tuition_id;
            $idtit = 'TITID' . $clase->tuition_id;
            if ($request->has($idval)) {
                // Add the detail to the array
                $details[] = [
                    'tuition_id' => $clase->tuition_id, // Assign the tuition ID
                    'title' => $request->input($idtit), // Decode the title
                    'value' => $request->input($idval), // Get the entered value
                ];
            }
        }
        // If there are details, assign them to the liquidation
        if (!empty($details)) {
            $liquidation->setDetailsAttribute($details);
            // Save the liquidation with the details
            $liquidation->save();
        }
        // Generate the textual summary (glosa) for the liquidation
        $glosa = self::generateGlosa($request, $workerId, $liquidation->id, $request->school_id);
        $liquidation->glosa = $glosa;
        $liquidation->save();
        // Return Liquidation
        return $liquidation;
    }

    /**
     * Generate the glosa (textual summary) for the liquidation.
     *
     * This method generates the payroll summary in HTML format. It loops through
     * the template and formats details accordingly.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $workerId
     * @param int $liquidationId
     * @param int $school_id
     * @return string
     */
    private static function generateGlosa($request, $workerId, $liquidationId, $school_id) {
        // Get the worker and template data
        $worker = Worker::find($workerId);
        $plantilla = Template::getTemplate($request->school_id, $worker->worker_type);
        // Get header data for the glosa
        $headerData = self::getHeaderGlosa($liquidationId, $workerId, $school_id);
        // Generate the details part of the glosa
        $details = '';
        foreach ($plantilla as $row) {
            $code = trim($row->code);
            $clase = $row->tuition_id;
            $ignoreZero = $row->ignore_zero;
            $parentheses = $row->parentheses;
            $clastit = self::getDetailByTuitionId($liquidationId, $clase, 'title');
            $clasval = self::getDetailByTuitionId($liquidationId, $clase, 'value');
            $clasval3 = $clasval;
            // We evaluate if the spreadsheet is divisible by 0, it has a value and if the value of the class is 0
            if (($ignoreZero == 1) && ($clasval == 0)) {
                continue; // Skip rows where the value is zero and should be ignored
            }
            // We evaluate the code of the template
            switch ($code) {
                case "N":
                    $details .= "<tr><td>" . ($clastit ? $clastit : "") . "</td></tr>";
                    break;
                case "NV":
                    $details .= "<tr><td>" . ($clastit ? $clastit : "") . "</td><td class='text-right'>$</td><td class='text-right'>" . $clasval . "</td></tr>";
                    break;
                case "NVV":
                    if ($clastit) {
                        $clasval3 = ($parentheses == 1) ? "($clasval)" : $clasval;
                        $details .= "<tr><td>" . $clastit . "</td><td class='text-right'>$</td><td class='text-right'>" . $clasval . "</td><td class='text-right'>$</td><td class='text-right'>" . $clasval3 . "</td></tr>";
                    }
                    break;
                case "N V":
                    if ($clastit) {
                        $clasval3 = ($parentheses == 1) ? "($clasval)" : $clasval;
                        $details .= "<tr><td>" . $clastit . "</td><td></td><td></td><td class='text-right'>$</td><td class='text-right'>" . $clasval3 . "</td></tr>";
                    }
                    break;
                case "_L_":
                case "LLL":
                    $details .= "<tr><td colspan='5' class='center'><hr/></td></tr>";
                    break;
                case "TEX":
                    $details .= "<tr><td colspan='5' class='center'><u>" . $clase . "</u></td></tr>";
                    break;
                case "":
                    $details .= "<tr><td>&nbsp;</td></tr>";
                    break;
            }
            $details .= "</tr>";
        }
        $details .= "</tbody></table>";
        // Generate the final glosa (summary) by rendering a view
        $glosa = view('liquidations.liquidationData', [
            'headerData' => $headerData,
            'details' => $details,
                ])->render();
        // Escape single quotes for storage
        $glosa = str_replace("'", "\'", $glosa);
        // Return glosa
        return $glosa;
    }

    /**
     * Generate the header data for the glosa (summary).
     *
     * This method retrieves worker and school data, along with the number of days worked.
     * It organizes the data to be used in the glosa header.
     *
     * @param int $liquidationId
     * @param int $workerId
     * @param int $school_id
     * @return array
     */
    public static function getHeaderGlosa($liquidationId, $workerId, $school_id) {
        $daysworkers = self::getDetailByTuitionId($liquidationId, "DIASTRABAJADOS", 'value');
        $absentdays = self::getDetailByTuitionId($liquidationId, "DIASNOTRABAJADOS", 'value');

        $dataGlosa = LiquidationHelper::getHeaderLiquidation($workerId, $school_id, now()->month);
        // We show the worker's data, school, month in which they work and their workload, and the current year
        $worker = $dataGlosa['worker'];
        $school = $dataGlosa['school'];
        $monthTxt = $dataGlosa['monthTxt'];
        $workload = $dataGlosa['workload'];
        $year = now()->year;
        // Organize header data to pass to the view
        $headerData = [
            'school_name' => $school->name,
            'school_rut' => $school->rut,
            'school_rbd' => $school->rbd,
            'month_txt' => $monthTxt,
            'year' => $year,
            'worker_name' => $worker->name,
            'worker_last_name' => $worker->last_name,
            'worker_rut' => $worker->rut,
            'workload' => $workload,
            'worker_function' => $worker->getFunctionWorkerTypes()[$worker->function_worker],
            'days_worked' => $daysworkers,
            'absent_days' => $absentdays
        ];
        // Return HeaderData
        return $headerData;
    }

    /**
     * Set the 'details' attribute.
     *
     * This method checks if there are existing details stored in the model's attributes.
     * If so, it decodes them from JSON and merges them with the new data provided.
     * If no existing details are found, it initializes an empty array.
     * The merged result is then encoded back into JSON and saved as the 'details' attribute.
     *
     * @param array $value The new details to be added.
     */
    public function setDetailsAttribute($value) {
        // If there are existing details, decode them and merge with the new ones
        if (isset($this->attributes['details'])) {
            $existingDetails = json_decode($this->attributes['details'], true);
        } else {
            $existingDetails = [];
        }
        // Merge the existing details with the new ones
        $mergedDetails = array_merge($existingDetails, $value);
        // Save the combined details in JSON format
        $this->attributes['details'] = json_encode($mergedDetails);
    }

    /**
     * Retrieve all unique years from the database.
     *
     * This method uses the 'distinct' function to ensure that only unique 'year' values are returned.
     * It then uses the 'pluck' function to fetch the 'year' column from the result,
     * returning an array of distinct years without duplicates.
     *
     * @return \Illuminate\Support\Collection An array of distinct years.
     */
    public static function getDistinctYears() {
        return self::distinct('year')->pluck('year');
    }

    /**
     * Retrieve a specific detail for a tuition by its ID.
     *
     * This method gets either the 'title' or 'value' for a specific tuition ID from the liquidation details.
     *
     * @param int $liquidationId
     * @param int $tuitionId
     * @param string $field
     * @return mixed
     */
    public static function getDetailByTuitionId($liquidationId, $tuitionId, $field) {
        $liquidation = self::find($liquidationId);
        if (!$liquidation) {
            return 0; // If the liquidation doesn't exist, return 0
        }
        // We evaluate the details of the liquidation
        $details = json_decode($liquidation->details, true);
        foreach ($details as $detail) {
            if (isset($detail['tuition_id']) && $detail['tuition_id'] == $tuitionId) {
                return $detail[$field]; // Return the requested field (title or value)
            }
        }
        // Return 0 if the tuition ID is not found
        return 0;
    }

    /**
     * Delete an existing liquidation for a worker, month, and year.
     *
     * This method deletes the liquidation if it exists for the given period and worker.
     *
     * @param int $month
     * @param int $year
     * @param int $workerId
     * @return int
     */
    private static function deleteLiquidation($month, $year, $workerId) {
        $liquidation = self::where('month', $month)
                ->where('year', $year)
                ->where('worker_id', $workerId)
                ->first(); // Find the first matching record

        if (!$liquidation) {
            return 1; // If no liquidation exists, return 1
        }

        $liquidation->delete(); // Otherwise, delete the liquidation
    }

    /**
     * Check if a liquidation exists for a worker, month, and year.
     *
     * This method checks if there is already a liquidation for the given month, year, and worker.
     *
     * @param int $month
     * @param int $year
     * @param int $workerId
     * @return bool
     */
    public static function exists($month, $year, $workerId) {
        $liquidacion = self::where('month', $month)
                ->where('year', $year)
                ->where('worker_id', $workerId)
                ->first(); // Returns the first result or null if not found
        // Return true if a liquidation exists, otherwise false
        return $liquidacion ? true : false;
    }

    /**
     * Get liquidations by worker type.
     *
     * This method retrieves liquidations based on the worker type for the given month, year, and school.
     *
     * @param int $month
     * @param int $year
     * @param int $type
     * @param int $schoolId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLiquidationsByType($month, $year, $type, $schoolId) {
        return self::where('month', $month)
                        ->where('year', $year)
                        ->whereHas('worker', function ($query) use ($type, $schoolId) {
                            $query->where('worker_type', $type)
                            ->where('school_id', $schoolId)
                            ->whereNull('settlement_date'); // Equivalent to fec_finiquito
                        })
                        ->get(); // Retrieve the liquidations for the specified worker type
    }

    /**
     * Define the relationship with the Worker model.
     *
     * A Liquidations belongs to a Worker.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function worker() {
        return $this->belongsTo(Worker::class);
    }

}
