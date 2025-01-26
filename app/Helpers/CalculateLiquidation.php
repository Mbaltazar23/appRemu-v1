<?php

namespace App\Helpers;

use App\Models\Insurance;
use App\Models\Parameter;
use App\Models\TmpLiquidation;
use App\Models\Tuition;

class CalculateLiquidation {

    // Removes excessive spaces from a string, leaving only one space between words.
    public static function removeExcessSpaces($st) {
        $st = trim($st); // Removes leading and trailing spaces from the string.
        // Replaces all double spaces with a single space until no double spaces remain.
        while (strstr($st, "  ") != "") {
            $st = str_replace("  ", " ", $st);
        }
        return $st; // Returns the processed string.
    }

    // Checks if a value is within specified limits and adjusts the value if it exceeds.
    public static function checkLimits($value, $tuitionId, $min, $max, $maxValue) {
        // Gets the unit value for the tuition type.
        $unitValue = Parameter::getParameterValue($tuitionId, 0, 0);
        if ($unitValue == 0) {
            $unitValue = 1; // If no unit value is found, assume it to be 1.
        }
        // Calculates the minimum and maximum limit values.
        $minLimitValue = $unitValue * $min;
        $maxLimitValue = $unitValue * $max;
        // If the value should be exactly equal to the minimum limit and it's not, return 0.
        if (($min == $max) && ($value != $minLimitValue) && ($min != 0)) {
            return 0;
        }
        // If the value is less than the minimum, return 0.
        if ($min > 0) {
            if ($value < $minLimitValue) {
                return 0;
            }
        }
        // If the value is greater than the maximum, return the maximum allowed value.
        if ($max > 0) {
            if ($value > $maxLimitValue) {
                return ($unitValue * $maxValue);
            }
        }
        // If the value is negative, adjust it to 0.
        if ($value < 0) {
            $value = 0;
        }
        return $value; // Returns the adjusted value within limits.
    }

    // Checks if the operation is valid for the current month.
    public static function checkApplication($months) {
        $currentMonth = date("n") - 1; // Gets the current month (0-indexed).
        $monthString = substr($months, $currentMonth, 1); // Extracts the corresponding month.
        // Evaluates if the months are available for processing.
        if ($months == "") {
            return true; // If no months are specified, it is considered valid.
        }
        // If the corresponding month is "1", the application is considered valid.
        if ($monthString == "1") {
            return true;
        } else {
            return false; // If it's not "1", the application is not valid.
        }
    }

    // Processes the calculation of a liquidation based on tuition parameters, worker type, etc.
    public static function processCalculation($tuitionId, $workerId, $workerTypeId, $schoolId) {
        if ($tuitionId == "") {
            return 0; // If no tuition ID is provided, return 0.
        }
        // Checks if the tuition already has a calculated value.
        $exists = self::alreadyExists($tuitionId);

        if ($exists != -1) {
            return $exists; // If it exists, return the previously calculated value.
        }
        // Retrieves the operations related to the tuition.
        $operations = Tuition::getTuitionAndOperationDetails($tuitionId, $workerTypeId, $schoolId);
        $act = 0;
        $inLiquidation = 0;
        if ($operations) {
            // Extracts the operation details.
            $operationType = $operations->type;
            $operation = $operations->operation;
            $unitLimit = $operations->limit_unit;
            $minLimit = $operations->min_limit;
            $maxLimit = $operations->max_limit;
            $maxValueLimit = $operations->max_value;
            $months = $operations->application;
            $inLiquidation = $operations->in_liquidation;
            $workerType = $operations->worker_type;
            // If the worker type matches or is not specified, and the application is valid, process the operation.
            if ((($workerTypeId == $workerType) || ($workerType == "")) && (self::checkApplication($months) == true)) {
                switch ($operationType) {
                    case "O": // Mathematical operation (+, -, *, /)
                        $operation = self::removeExcessSpaces($operation); // Removes unnecessary spaces.
                        $parts = explode(" ", $operation); // Splits the operation into parts.
                        $mem = 0;
                        $act = 0;
                        $op = "";
                        foreach ($parts as $item) {
                            // Executes the mathematical operation based on the operator found.
                            if (is_numeric($item)) {
                                switch ($op) {
                                    case "+":
                                        $act = $act + $item;
                                        $op = "";
                                        break;
                                    case "-":
                                        $act = $act - $item;
                                        $op = "";
                                        break;
                                    case "*":
                                        $act = $act * $item;
                                        $op = "";
                                        break;
                                    case "/":
                                        if ($item == 0) {
                                            $act = 0;
                                        } else {
                                            $act = $act / $item;
                                            $op = "";
                                        }
                                        break;
                                    default:
                                        $act = $item;
                                        $op = "";
                                }
                            } elseif (($item == "*") || ($item == "/") || ($item == "+") || ($item == "-")) {
                                $op = $item;
                            } elseif (($item == "M+") || ($item == "M-") || ($item == "MR") || ($item == "MC")) {
                                if ($item == "M+") {
                                    $mem = $mem + $act;
                                }
                                if ($item == "M-") {
                                    $mem = $mem - $act;
                                }
                                if ($item == "MR") {
                                    $act = $mem;
                                }
                                if ($item == "MC") {
                                    $mem = 0;
                                }
                            } else {
                                // If the item is another type, make a recursive call.
                                $res = self::processCalculation($item, $workerId, $workerTypeId, $schoolId);
                                switch ($op) {
                                    case "+":
                                        $act = $act + $res;
                                        break;
                                    case "-":
                                        $act = $act - $res;
                                        break;
                                    case "*":
                                        $act = $act * $res;
                                        break;
                                    case "/":
                                        if ($res == 0) {
                                            $act = 0;
                                        } else {
                                            $act = $act / $res;
                                        }
                                        break;
                                    default:
                                        $act = $res;
                                        $op = "";
                                        break;
                                }
                            }
                        }
                        // Applies limits and returns the calculated value.
                        $act = round(self::checkLimits($act, $unitLimit, $minLimit, $maxLimit, $maxValueLimit));
                        break;
                    // Direct parameter
                    case "P":
                        $act = Parameter::getParameterValue($tuitionId, $workerId, $schoolId); // Retrieves the parameter value.
                        $unitParam = Parameter::getUnitByTuitionWorkerAndSchool($tuitionId, $workerId, $schoolId);
                        if ($unitParam != "") {
                            // If there is an associated unit, adjusts the value based on the unit.
                            $unitValue = Parameter::getParameterValue($unitParam, $workerId, $schoolId);
                            $act = $act * $unitValue;
                        }
                        break;
                    // Sum of Parameter
                    case "S":
                        if ($tuitionId != "SUMACARGASTODOS") {
                            $act = Parameter::getSumValueByTuitionSchoolAndWorkerType($operation, $schoolId, $workerTypeId);
                        } else {
                            // If it's "SUMACARGASTODOS", sums the values for all workers.
                            $act = Parameter::getSumValueByTuitionSchoolAndWorkerType($operation, $schoolId, 1) + Parameter::getSumValueByTuitionSchoolAndWorkerType($operation, $schoolId, 2);
                        }
                        break;
                }
            }
        }
        // Special cases, like the "ASIGNACIONFAMILIAR".
        if ($tuitionId == "ASIGNACIONFAMILIAR") {
            if (0.8333 >= Parameter::getParameterValue("FACTORASIST", $workerId, $schoolId)) {
                $act = round($act * Parameter::getParameterValue("FACTORASIST", $workerId, $schoolId) / 0.8333);
            }
        }
        // Returns the title associated with the parameter and makes adjustments according to AFP or ISAPRE.
        $title = Parameter::getTitleByParameter($tuitionId, $workerId, $schoolId);
        // Evaluates if the selected class is from any Insurance that the worker has
        if ($tuitionId == "AFP") {
            $title = Parameter::getDescriptionByCode("AFPTRABAJADOR", $workerId, $schoolId);
            $title = "(" . Parameter::getParameterValue("COTIZACIONAFP", $workerId, $schoolId) . " %) " . Insurance::getNameInsurance($title);
        }
        if ($tuitionId == "SALUD") {
            $title = Parameter::getDescriptionByCode("ISAPRETRABAJADOR", $workerId, $schoolId);
            $title = "(" . Parameter::getParameterValue("COTIZACIONISAPRE", $workerId, $schoolId) . " %) " . Insurance::getNameInsurance($title);
        }

        self::saveInTemporaryTable($tuitionId, $title, $act, $inLiquidation);

        return $act; // Returns the final calculated value.
    }

    /**
     * Saves a calculation in the "temporary table" (in the session).
     *
     * @param string $tuition_id The ID of the calculation.
     * @param string $title      The title of the calculation.
     * @param float  $value      The value of the calculation.
     * @param int    $inLiquidation Indicates whether it's in liquidation (1 or 0).
     */
    public static function saveInTemporaryTable($id, $title, $value, $inLiquidation) {
        // Checks if the tuition_id already exists in the temporary table 'tmp_liquidation'.
        $exists = TmpLiquidation::where('tuition_id', $id)->exists();
        // If it doesn't exist, inserts a new record with the provided data.
        if (!$exists) {
            TmpLiquidation::create([
                'tuition_id' => $id, // Assigns the calculation ID.
                'title' => $title, // Assigns the title of the calculation.
                'value' => $value, // Assigns the value of the calculation.
                'in_liquidation' => $inLiquidation, // Indicates if it's in liquidation.
            ]);
        }
    }

    /**
     * Checks if a calculation already exists in the temporary table.
     *
     * @param string $id The ID of the calculation.
     * @return mixed The value of the calculation if it exists, or -1 if it doesn't exist.
     */
    public static function alreadyExists($id) {
        // Checks if a record exists in the 'tmp_liquidation' table with the 'tuition_id'.
        $record = TmpLiquidation::where('tuition_id', $id)->first();
        // If the calculation exists, returns its value; if not, returns -1.
        return $record ? $record->value : -1;
    }

}
