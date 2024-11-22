<?php

namespace App\Helpers;

use App\Models\Absence;
use App\Models\Insurance;
use App\Models\License;
use App\Models\Parameter;
use App\Models\School;
use App\Models\Tuition;
use App\Models\Worker;
use Illuminate\Support\Facades\Session;

class LiquidationHelper
{

    // Remove unnecessary spaces from a string
    public function removeExcessSpaces($st)
    {
        $st = trim($st);
        while (strstr($st, "  ") != "") {
            $st = str_replace("  ", " ", $st);
        }
        return $st;
    }

    // Check limits. If the value exceeds the limit, return the limit. If below the limit, return 0.
    public function checkLimits($value, $tuitionId, $min, $max, $maxValue)
    {
        $unitValue = Parameter::getParameterValue($tuitionId, 0, 0);
        if ($unitValue == 0) {
            $unitValue = 1;
        }

        $minLimitValue = $unitValue * $min;
        $maxLimitValue = $unitValue * $max;

        if (($min == $max) && ($value != $minLimitValue) && ($min != 0)) {
            return 0;
        }

        if ($min > 0 && $value < $minLimitValue) {
            return 0;
        }
        if ($max > 0 && $value > $maxLimitValue) {
            return $unitValue * $maxValue;
        }
        if ($value < 0) {
            $value = 0;
        }

        return $value;
    }

    // Check if an operation should be applied in the current month
    public function checkApplication($months)
    {
        $currentMonth = date("n") - 1;
        $monthString = substr($months, $currentMonth, 1);

        if ($months == "") {
            return true;
        }

        if ($monthString == 1) {
            return true;
        } else {
            return false;

        }
    }

    // Recursive calculation process for tuition (class)
    public function processCalculation($tuitionId, $workerId, $workerTypeId, $schoolId)
    {
        if ($tuitionId == "") {
            return 0;
        }

        $exists = self::alreadyExists($tuitionId);

        if ($exists != -1) {
            return $exists;
        }

        // Get operations related to this tuition
        $operations = Tuition::getOperationsByTuitionAndWorkerType($tuitionId, $workerTypeId, $schoolId);
        $act = 0;
        $inLiquidation = 0;

        if ($operations) {
            $row = $operations;
            $operationType = $row->type;
            $operation = $row->operation;
            $unitLimit = $row->limit_unit;
            $minLimit = $row->min_limit;
            $maxLimit = $row->max_limit;
            $maxValueLimit = $row->max_value;
            $months = $row->application;
            $inLiquidation = $row->in_liquidation;
            $workerType = $row->worker_type;

            if ((($workerTypeId == $workerType) || ($workerType == "")) && self::checkApplication($months)) {
                switch ($operationType) {
                    case "O": // Operation
                        $operation = self::removeExcessSpaces($operation);
                        $parts = explode(" ", $operation);
                        $mem = 0;
                        $act = 0;
                        $op = "";

                        foreach ($parts as $item) {
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
                                        $act = ($item == 0) ? 0 : ($act / $item);
                                        $op = "";
                                        break;
                                    default:
                                        $act = $item;
                                        $op = "";
                                        break;
                                }
                            } elseif (($item == "*") or ($item == "/") or ($item == "+") or ($item == "-")) {
                                $op = $item;
                            } elseif (($item == "M+") or ($item == "M-") or ($item == "MR") or ($item == "MC")) {
                                if ($item == "M+") {$mem = $mem + $act;}
                                if ($item == "M-") {
                                    $mem = $mem - $act;
                                }

                                if ($item == "MR") {$act = $mem;}
                                if ($item == "MC") {
                                    $mem = 0;
                                }
                            } else {
                                // Recursive call for other tuition
                                $res = self::processCalculation($item, $workerId, $workerTypeId, $schoolId);
                                switch ($op) {
                                    case "+":
                                        $act = $act + $res;
                                        $op = "";
                                        break;
                                    case "-":
                                        $act = $act - $res;
                                        $op = "";
                                        break;
                                    case "*":
                                        $act = $act * $res;
                                        $op = "";
                                        break;
                                    case "/":
                                        if ($res == 0) {$act = 0;} else { $act = $act / $res;
                                            $op = "";}
                                        break;
                                    default:
                                        $act = $res;
                                        $op = "";
                                        break;
                                }
                            }
                        }

                        // Check limits
                        $act = round(self::checkLimits($act, $unitLimit, $minLimit, $maxLimit, $maxValueLimit));
                        break;

                    case "P": // Parameter
                        $act = Parameter::getParameterValue($tuitionId, $workerId, $schoolId);
                        //dd($act);
                        $unitParam = Parameter::getUnitByTuitionWorkerAndSchool($tuitionId, $workerId, $schoolId);
                        if ($unitParam != "") {
                            $unitValue = Parameter::getParameterValue($unitParam, $workerId, $schoolId);
                            $act = $act * $unitValue;
                        }
                        break;

                    case "S": // Sum Parameter
                        if ($tuitionId != "SUMACARGASTODOS") {
                            $act = Parameter::getSumValueByTuitionSchoolAndWorkerType($operation, $schoolId, $workerTypeId);
                        } else {
                            $act = Parameter::getSumValueByTuitionSchoolAndWorkerType($operation, $schoolId, 1) + Parameter::getSumValueByTuitionSchoolAndWorkerType($operation, $schoolId, 2);
                        }
                        break;
                }
            }
        }

        // Special case for "Family Allowance" tuition
        if ($tuitionId == "ASIGNACIONFAMILIAR") {
            if (0.8333 >= Parameter::getParameterValue("FACTORASIST", $workerId, $schoolId)) {
                $act = round($act * Parameter::getParameterValue("FACTORASIST", $workerId, $schoolId) / 0.8333);
            }
        }
        $title = Parameter::getTitleByParameter($tuitionId, $workerId, $schoolId);
        // Additional logic for AFP or ISAPRE (like your original code)
        if ($tuitionId == "AFP") {
            $title = Parameter::getDescriptionByCode("AFPTRABAJADOR", $workerId, $schoolId);
            $title = "(" . Parameter::getParameterValue("COTIZACIONAFP", $workerId, $schoolId) . " %) " . Insurance::getNameInsurance($title);
        } elseif ($tuitionId == "SALUD") {
            $title = Parameter::getDescriptionByCode("ISAPRETRABAJADOR", $workerId, $schoolId);
            $title = "(" . Parameter::getParameterValue("COTIZACIONISAPRE", $workerId, $schoolId) . " %) " . Insurance::getNameInsurance($title);
        }

        // Save to temporary table in session
        self::saveInTemporaryTable($tuitionId, $title, $act, $inLiquidation);

        return $act;
    }

    // Verificar si el trabajador tiene los parámetros de AFP e ISAPRE (o Fonasa)
    public function checkAFPandISAPRE($workerId, $schoolId)
    {
        // Verificamos que los parámetros de AFP e ISAPRE estén establecidos
        $afp = Parameter::getDescriptionByTuitionWorkerAndSchool('AFPTRABAJADOR', $workerId, $schoolId);
        $isapre = Parameter::getDescriptionByTuitionWorkerAndSchool('ISAPRETRABAJADOR', $workerId, $schoolId);

        if (empty($afp) || empty($isapre)) {
            // Verificamos si el trabajador está jubilado
            $jub = Parameter::getParameterValue('JUBILADO', $workerId, $schoolId);
            if ($jub == 0) {
                return "Para crear la liquidación es necesario que el trabajador esté afiliado a una AFP y a una ISAPRE (o Fonasa)";
            }

            // Si el trabajador está jubilado pero no tiene ISAPRE
            if ($jub == 1 && empty($isapre)) {
                return "Para crear la liquidación es necesario que el trabajador esté afiliado a una AFP y a una ISAPRE (o Fonasa)";
            }
        }
        return; // No hay errores
    }

    // Calcular el mes anterior y el año anterior y la data completa
    public function getLiquidationData($workerId, $schoolId)
    {
        $tuitionFirst = 'TOTALAPAGAR';

        $worker = Worker::find($workerId);
        $workerType = $worker->worker_type;

        $mesl = (int) date("n"); // Convert the month to an integer
        $cierremes = (int) Parameter::getValueByName("CIERREMES", $schoolId);
        //dd($cierremes);
        if ($mesl == 1) {
            $mount = 12; // December (previous year)
            $yearant = (int) date("y") - 1; // Current year (2 digits) minus 1
            $yearantina = (int) date("Y") - 1; // Current year (4 digits) minus 1
        } else {
            $mount = (int) ($mesl - 1); // Previous month
            $yearant = (int) date("y"); // Current year (2 digits)
            $yearantina = (int) date("Y"); // Current year (4 digits)
        }

        $workload = Parameter::where('name', "CARGAHORARIA")->where('worker_id', $workerId)->where('school_id', $schoolId)->value('value');

        $minutesAbsence = self::getTotalAbsenceMinutes($workerId, $mount, $mesl, $yearantina, $cierremes);
        //dd($minutesAbsence);
        if ($workerType == Worker::WORKER_TYPE_TEACHER) {
            $factorasist = self::calculateAttendanceFactorForTeacher($workerId, $mount, $mesl, $yearant, $cierremes, $workload, $minutesAbsence);
        } else {
            $factorasist = self::calculateAttendanceFactorForNonTeacher($workerId, $mount, $mesl, $yearant, $cierremes, $workload, $minutesAbsence);
        }
        self::updateOrInsertFactorasist($workerId, $schoolId, $factorasist);
        //self::createTemporaryTable();
        self::processCalculation($tuitionFirst, $workerId, $workerType, $schoolId);
        self::saveInTemporaryTable("DIASTRABAJADOS", "Dias trabajados", 0, 1);
    }

    public function getHeaderLiquidation($workerId, $schoolId, $month)
    {
        $school = School::find($schoolId);

        $monthTxt = MonthHelper::integerToMonth($month);
        $worker = Worker::find($workerId);

        $workload = Parameter::getValueByName("CARGAHORARIA", $workerId, $schoolId);
        if ($workload < 10) {
            $workload = "0" . $workload;
        }

        return [
            'school' => $school,
            'worker' => $worker,
            'monthTxt' => $monthTxt,
            'workload' => $workload,
        ];
    }

    // Obtener el total de minutos de inasistencia para un trabajador
    public function getTotalAbsenceMinutes($workerId, $month, $mesl, $year, $cierremes)
{
        return Absence::sumAbsenceMinutes($workerId, $month, $year, $cierremes, 32) +
        Absence::sumAbsenceMinutes($workerId, $mesl, (int) date("Y"), 0, $cierremes + 1);
    }

    // Calcular las horas de licencia para un trabajador
    public function getLicenseHours($workerId, $month, $mesl, $year, $cierremes)
    {
        return License::sumLicenseHours($workerId, $month, $year, $cierremes, 32) +
        License::sumLicenseHours($workerId, $mesl, (int) date("Y"), 0, $cierremes + 1);
    }
// Calcular el factor de asistencia para trabajadores docentes
    public function calculateAttendanceFactorForTeacher($workerId, $previousMonth, $mesl, $previousYear, $cierremes, $hourlyRate, $absenceMinutes)
    {
        $licenseHours = self::getLicenseHours($workerId, $previousMonth, $mesl, $previousYear, $cierremes);
        // Asegurarse de que el denominador no sea cero
        $denominator = $hourlyRate * 60 * 4;
        if ($denominator == 0) {
            // Aquí puedes manejar el error, por ejemplo, retornando 0 o lanzando una excepción
            return 0;
        }
        return 1 - (($hourlyRate * $licenseHours * 8 + $absenceMinutes) / $denominator);
    }

    // Calcular el factor de asistencia para trabajadores no docentes
    public function calculateAttendanceFactorForNonTeacher($workerId, $previousMonth, $mesl, $previousYear, $cierremes, $hourlyRate, $absenceMinutes)
    {
        $licenseDays = self::sumLicenseDays($workerId, $previousMonth, $mesl, $previousYear, $cierremes);

        // Asegurarse de que el denominador no sea cero
        $denominator = $hourlyRate * 60 * 4;
        if ($denominator == 0) {
            // Aquí también puedes manejar el error, retornando 0 o lanzando una excepción
            return 0;
        }

        return 1 - (($hourlyRate * $licenseDays * 8 + $absenceMinutes) / $denominator);
    }

    // Obtener los días de licencia de un trabajador

    public function sumLicenseDays($workerId, $month, $mesl, $year, $cierremes)
    {
        return License::sumDaysLicence($workerId, $month, $year, $cierremes, 32) + License::sumDaysLicence($workerId, $mesl, (int) date("y"), 0, $cierremes + 1);
    }

    // Actualizar o insertar el valor de un parámetro
    public function updateOrInsertFactorasist($workerId, $schoolId, $value)
    {
        if (Parameter::exists('FACTORASIST', $workerId, $schoolId)) {
            Parameter::updateOrInsertParamValue('FACTORASIST', $workerId, $schoolId, $value);
        } else {
            Parameter::updateOrInsertParamValue('FACTORASIST', $workerId, $schoolId, $value);
        }
    }
    /**
     * Create a "temporary table" in the session to store liquidation calculations.
     * The "table" is simply an array stored in the session.
     */
    public function createTemporaryTable()
    {
        // If the temporary table does not exist, initialize it
        if (!Session::has('tmp')) {
            Session::put('tmp', []);
        }
    }

    /**
     * Save a calculation in the "temporary table" (session).
     *
     * @param string $id         The ID of the calculation
     * @param string $title      The title of the calculation
     * @param float  $value      The value of the calculation
     * @param int    $inLiquidation Indicates if it is in liquidation (1 or 0)
     */
    public function saveInTemporaryTable($id, $title, $value, $inLiquidation)
    {
        // Retrieve the "temporary table" from the session
        $tmp = Session::get('tmp');

        // Creamos el objeto LiquidationRecord
        $record = new LiquidationRecord($id, $title, $value, $inLiquidation);

        // Guardamos el objeto en la sesión con el ID como clave
        $tmp[$id] = $record;

        // Guardamos la "tabla temporal" de vuelta en la sesión
        Session::put('tmp', $tmp);

        // Store the "temporary table" back in the session
        Session::put('tmp', $tmp);
    }

    public function alreadyExists($id)
    {
        // Verifica si existe la sesión
        if (!Session::has('tmp')) {
            return -1; // Retorna -1 si no hay sesión activa
        }

        // Recupera los datos de la "tabla temporal" desde la sesión
        $tmp = Session::get('tmp', []);

        // Verifica si el ID existe en el arreglo de la sesión
        return isset($tmp[$id]) ? true : false;
    }

    /**
     * Retrieve a calculation from the "temporary table" by its ID.
     *
     * @param string $id The ID of the calculation
     * @return mixed The calculation or null if it does not exist
     */
    public function getFromTemporaryTable($id)
    {
        // Retrieve the "temporary table" from the session
        $tmp = Session::get('tmp', []);

        // If the ID exists, return it
        return isset($tmp[$id]) ? $tmp[$id] : null;
    }
}
