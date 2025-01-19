<?php
namespace App\Helpers;

use App\Models\Absence;
use App\Models\License;
use App\Models\Parameter;
use App\Models\School;
use App\Models\Worker;

class LiquidationHelper
{
    // Check if the worker has AFP and ISAPRE (or Fonasa) parameters
    public function checkAFPandISAPRE($workerId, $schoolId)
    {
        // Get the AFP and ISAPRE parameters for the worker at the given school
        $afp = Parameter::getDescriptionByTuitionWorkerAndSchool('AFPTRABAJADOR', $workerId, $schoolId);
        $isapre = Parameter::getDescriptionByTuitionWorkerAndSchool('ISAPRETRABAJADOR', $workerId, $schoolId);
        // If AFP or ISAPRE are missing, check the worker's retirement status
        if (empty($afp) || empty($isapre)) {
            // Get the retirement status of the worker
            $jub = Parameter::getParameterValue('JUBILADO', $workerId, $schoolId);
            // If the worker is not retired, return an error about missing AFP or ISAPRE
            if ($jub == 0) {
                return "Para crear la liquidación es necesario que el trabajador esté afiliado a una AFP y a una ISAPRE (o Fonasa)";
            }
            // If the worker is retired but missing ISAPRE, also return an error
            if ($jub == 1 && empty($isapre)) {
                return "Para crear la liquidación es necesario que el trabajador esté afiliado a una AFP y a una ISAPRE (o Fonasa)";
            }
        }
        return;
    }
    // Calculate previous month, previous year, and all the necessary data for liquidation
    public function getLiquidationData($workerId, $schoolId)
    {
        // Get the worker object based on the workerId
        $worker = Worker::find($workerId);
        // Get the worker's type (e.g., teacher or non-teacher)
        $workerType = $worker->worker_type;
        // Get the current month as an integer (1-12)
        $mesl = date("n");
        // Get the month closure parameter for the worker at the given school
        $cierremes = Parameter::getParameterValue('CIERREMES', $workerId, $schoolId);
        // If the current month is January (1), set the previous month to December (12) and adjust the year
        if ($mesl == 1) {
            $mount      = 12;            // December (previous year)
            $yearant    = date("y") - 1; // Current year (2 digits) minus 1
            $yearantina = date("Y") - 1; // Current year (4 digits) minus 1
        } else {
            $mount      = ($mesl - 1); // Set to the previous month (e.g., 2 for January, 3 for February)
            $yearant    = date("y");   // Current year (2 digits)
            $yearantina = date("Y");   // Current year (4 digits)
        }
        // Get the worker's weekly workload (hours per week)
        $workload = Parameter::getParameterValue("CARGAHORARIA", $workerId, $schoolId);
        // Calculate the total minutes the worker was absent during the given period
        $minutesAbsence = self::getTotalAbsenceMinutes($workerId, $mount, $mesl, $yearantina, $cierremes);
        // Based on the worker's type, calculate the attendance factor (days for non-teachers, hours for teachers)
        if ($workerType == Worker::WORKER_TYPE_TEACHER) {
            // For teachers, calculate the attendance factor based on license hours and absences
            $factorasist = self::calculateAttendanceFactorForTeacher($workerId, $mount, $mesl, $yearant, $cierremes, $workload, $minutesAbsence);
        } else {
            // For non-teachers, calculate the attendance factor based on license days and absences
            $factorasist = self::calculateAttendanceFactorForNonTeacher($workerId, $mount, $mesl, $yearant, $cierremes, $workload, $minutesAbsence);
        }
        // Update or insert the attendance factor into the database for the worker
        self::updateOrInsertFactorasist($workerId, $schoolId, $factorasist);
        // Proceed to calculate the total salary based on the worker's data
        CalculateLiquidation::processCalculation("TOTALAPAGAR", $workerId, $workerType, $schoolId);
        // Save the worked days into the temporary table for further processing
        CalculateLiquidation::saveInTemporaryTable("DIASTRABAJADOS", "Dias trabajados", 0, 1);
    }
    // Get the header information for the liquidation report
    public static function getHeaderLiquidation($workerId, $schoolId, $month)
    {
        // Get the school object based on the schoolId
        $school = School::find($schoolId);
        // Convert the month integer to its corresponding name (e.g., "January")
        $monthTxt = MonthHelper::integerToMonth($month);
        // Get the worker object based on the workerId
        $worker = Worker::find($workerId);
        // Get the worker's weekly workload (hours per week)
        $workload = Parameter::getParameterValue("CARGAHORARIA", $workerId, $schoolId);
        // Ensure the workload is formatted with two digits if less than 10
        if ($workload < 10) {
            $workload = "0" . $workload;
        }

        return [
            'school'   => $school,   // School object with all relevant details
            'worker'   => $worker,   // Worker object with all relevant details
            'monthTxt' => $monthTxt, // Full month name (e.g., "January")
            'workload' => $workload, // Worker workload formatted as "XX" hours
        ];
    }
    // Get the total absence minutes for a worker for the given month and year
    public function getTotalAbsenceMinutes($workerId, $month, $mesl, $year, $cierremes)
    {
        // Sum up the absence minutes from both the current and previous periods
        return Absence::sumAbsenceMinutes($workerId, $month, $year, $cierremes, 32) +
        Absence::sumAbsenceMinutes($workerId, $mesl, date("Y"), 0, $cierremes + 1);
    }
    // Get the total license hours for a worker for the given month and year
    public function getLicenseHours($workerId, $month, $mesl, $year, $cierremes)
    {
        // Sum up the license hours from both the current and previous periods
        return License::sumLicenseHours($workerId, $month, $year, $cierremes, 32) +
        License::sumLicenseHours($workerId, $mesl, date("y"), 0, $cierremes + 1);
    }
    // Get the total license days for a worker for the given month and year
    public function sumLicenseDays($workerId, $month, $mesl, $year, $cierremes)
    {
        // Sum up the license days from both the current and previous periods
        return License::sumDaysLicence($workerId, $month, $year, $cierremes, 32) +
        License::sumDaysLicence($workerId, $mesl, date("y"), 0, $cierremes + 1);
    }
    // Calculate the attendance factor for teachers based on their license hours and absence minutes
    public function calculateAttendanceFactorForTeacher($workerId, $previousMonth, $mesl, $previousYear, $cierremes, $hourlyRate, $absenceMinutes)
    {
        // Get the total license hours for the previous month and year
        $licenseHours = self::getLicenseHours($workerId, $previousMonth, $mesl, $previousYear, $cierremes);
        // Ensure the denominator is not zero to avoid division by zero error
        $denominator = $hourlyRate * 60 * 4;
        // Calculate and return the attendance factor based on the worker's attendance and absences
        return 1 - ($hourlyRate * $licenseHours * 8 + $absenceMinutes) / ($denominator);
    }

    // Calculate the attendance factor for non-teachers based on their license days and absence minutes
    public function calculateAttendanceFactorForNonTeacher($workerId, $previousMonth, $mesl, $previousYear, $cierremes, $hourlyRate, $absenceMinutes)
    {
        // Get the total license days for the previous month and year
        $licenseDays = self::sumLicenseDays($workerId, $previousMonth, $mesl, $previousYear, $cierremes);
        // Ensure the denominator is not zero to avoid division by zero error
        $denominator = $hourlyRate * 60 * 4;
        // Calculate and return the attendance factor based on the worker's attendance and absences
        return 1 - ($hourlyRate * $licenseDays * 8 + $absenceMinutes) / ($denominator);
    }
    // Update or insert the attendance factor for the worker in the database
    public function updateOrInsertFactorasist($workerId, $schoolId, $value)
    {
        // Check if the attendance factor already exists for the worker and school
        if (Parameter::exists('FACTORASIST', $workerId, $schoolId)) {
            // If it exists, update the value
            Parameter::updateOrInsertParamValue('FACTORASIST', $workerId, $schoolId, "Factor de asistencia", $value);
        } else {
            // If it doesn't exist, insert the new value
            Parameter::updateOrInsertParamValue('FACTORASIST', $workerId, $schoolId, "Factor de asistencia", $value);
        }
    }
}
