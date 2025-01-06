<?php

namespace App\Helpers;

use App\Models\Absence;
use App\Models\License;
use App\Models\Parameter;
use App\Models\School;
use App\Models\Worker;

class LiquidationHelper
{
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
        return; 
    }

    // Calcular el mes anterior y el año anterior y la data completa
    public function getLiquidationData($workerId, $schoolId)
    {
        //$tuitionFirst = 'TOTALAPAGAR';
        $worker = Worker::find($workerId);
        $workerType = $worker->worker_type;

        $mesl = date("n"); // Convert the month to an integer
        $cierremes = Parameter::getParameterValue('CIERREMES', $workerId, $schoolId);

        if ($mesl == 1) {
            $mount = 12; // December (previous year)
            $yearant = date("y") - 1; // Current year (2 digits) minus 1
            $yearantina = date("Y") - 1; // Current year (4 digits) minus 1
        } else {
            $mount = ($mesl - 1); // Previous month
            $yearant = date("y"); // Current year (2 digits)
            $yearantina = date("Y"); // Current year (4 digits)
        }

        $workload = Parameter::getParameterValue("CARGAHORARIA", $workerId, $schoolId);

        $minutesAbsence = self::getTotalAbsenceMinutes($workerId, $mount, $mesl, $yearantina, $cierremes);
        //dd($minutesAbsence);
        if ($workerType == Worker::WORKER_TYPE_TEACHER) {
            $factorasist = self::calculateAttendanceFactorForTeacher($workerId, $mount, $mesl, $yearant, $cierremes, $workload, $minutesAbsence);
        } else {
            $factorasist = self::calculateAttendanceFactorForNonTeacher($workerId, $mount, $mesl, $yearant, $cierremes, $workload, $minutesAbsence);
        }
        self::updateOrInsertFactorasist($workerId, $schoolId, $factorasist);
        //self::createTemporaryTable();
        //CalculateLiquidation::procesingCalculate($workerId, $workerType, $schoolId);
        CalculateLiquidation::processCalculation("TOTALAPAGAR", $workerId, $workerType, $schoolId);
        CalculateLiquidation::saveInTemporaryTable("DIASTRABAJADOS", "Dias trabajados", 0, 1);
    }

    public static function getHeaderLiquidation($workerId, $schoolId, $month)
    {
        $school = School::find($schoolId);

        $monthTxt = MonthHelper::integerToMonth($month);
        $worker = Worker::find($workerId);

        $workload = Parameter::getParameterValue("CARGAHORARIA", $workerId, $schoolId);
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
        Absence::sumAbsenceMinutes($workerId, $mesl, date("Y"), 0, $cierremes + 1);
    }

    // Calcular las horas de licencia para un trabajador
    public function getLicenseHours($workerId, $month, $mesl, $year, $cierremes)
    {
        return License::sumLicenseHours($workerId, $month, $year, $cierremes, 32) +
        License::sumLicenseHours($workerId, $mesl, date("y"), 0, $cierremes + 1);
    }

   // Obtener los días de licencia de un trabajador

   public function sumLicenseDays($workerId, $month, $mesl, $year, $cierremes)
   {
       return License::sumDaysLicence($workerId, $month, $year, $cierremes, 32) + 
       License::sumDaysLicence($workerId, $mesl, date("y"), 0, $cierremes + 1);
   }

    // Calcular el factor de asistencia para trabajadores docentes
    public function calculateAttendanceFactorForTeacher($workerId, $previousMonth, $mesl, $previousYear, $cierremes, $hourlyRate, $absenceMinutes)
    {
        $licenseHours = self::getLicenseHours($workerId, $previousMonth, $mesl, $previousYear, $cierremes);
        // Asegurarse de que el denominador no sea cero
        $denominator = $hourlyRate * 60 * 4;

        return 1 - ($hourlyRate * $licenseHours * 8 + $absenceMinutes) / ($denominator);
    }
    // Calcular el factor de asistencia para trabajadores no docentes
    public function calculateAttendanceFactorForNonTeacher($workerId, $previousMonth, $mesl, $previousYear, $cierremes, $hourlyRate, $absenceMinutes)
    {
        $licenseDays = self::sumLicenseDays($workerId, $previousMonth, $mesl, $previousYear, $cierremes);
        // Asegurarse de que el denominador no sea cero
        $denominator = $hourlyRate * 60 * 4;

        return 1 - ($hourlyRate * $licenseDays * 8 + $absenceMinutes) / ($denominator);
    }

    // Actualizar o insertar el valor de un parámetro
    public function updateOrInsertFactorasist($workerId, $schoolId, $value)
    {
        if (Parameter::exists('FACTORASIST', $workerId, $schoolId)) {
            Parameter::updateOrInsertParamValue('FACTORASIST', $workerId, $schoolId,"Factor de asistencia",  $value);
        } else {
            Parameter::updateOrInsertParamValue('FACTORASIST', $workerId, $schoolId,"Factor de asistencia", $value);
        }
    }
}
