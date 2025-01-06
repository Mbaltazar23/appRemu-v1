<?php

namespace App\Helpers;

use App\Models\Insurance;
use App\Models\Parameter;
use App\Models\TmpLiquidation;
use App\Models\Tuition;

class CalculateLiquidation
{
    // Elimina los espacios en exceso de una cadena, dejando solo un espacio entre palabras.
    public static function removeExcessSpaces($st)
    {
        $st = trim($st); // Elimina los espacios en blanco al inicio y final de la cadena.
        // Reemplaza todos los espacios dobles por uno solo, hasta que no haya más espacios dobles.
        while (strstr($st, "  ") != "") {
            $st = str_replace("  ", " ", $st);
        }
        return $st; // Retorna la cadena procesada.
    }

    // Verifica si un valor está dentro de los límites especificados, y ajusta el valor si excede.
    public static function checkLimits($value, $tuitionId, $min, $max, $maxValue)
    {
        // Obtiene el valor unitario para el tipo de matrícula.
        $unitValue = Parameter::getParameterValue($tuitionId, 0, 0);
        if ($unitValue == 0) {
            $unitValue = 1; // Si no se encuentra un valor unitario, se asume que es 1.
        }
        // Calcula los valores límite mínimo y máximo.
        $minLimitValue = $unitValue * $min;
        $maxLimitValue = $unitValue * $max;
        // Si el valor debe ser exactamente igual al límite mínimo y no lo es, retorna 0.
        if (($min == $max) && ($value != $minLimitValue) && ($min != 0)) {
            return 0;
        }
        // Si el valor es menor al mínimo, retorna 0.
        if ($min > 0) {
            if ($value < $minLimitValue) {
                return 0;
            }
        }
        // Si el valor es mayor al máximo, retorna el valor máximo permitido.
        if ($max > 0) {
            if ($value > $maxLimitValue) {
                return $unitValue * $maxValue;
            }
        }
        // Si el valor es negativo, lo ajusta a 0.
        if ($value < 0) {
            $value = 0;
        }
        return $value; // Retorna el valor ajustado dentro de los límites.
    }

    // Verifica si la aplicación de la operación es válida para el mes actual.
    public static function checkApplication($months)
    {
        $currentMonth = date("n") - 1; // Obtiene el mes actual (0-indexed).
        $monthString = substr($months, $currentMonth, 1); // Extrae el mes correspondiente.

        if ($months == "") {
            return true; // Si no hay meses especificados, se considera válido.
        }
        // Si el mes correspondiente es "1", se considera válida la aplicación.
        if ($monthString == "1") {
            return true;
        } else {
            return false; // Si no es "1", la aplicación no es válida.
        }
    }

    // Procesa el cálculo de una liquidación según los parámetros de matrícula, tipo de trabajador, etc.
    public static function processCalculation($tuitionId, $workerId, $workerTypeId, $schoolId)
    {
        if ($tuitionId == "") {
            return 0; // Si no se proporciona un ID de matrícula, retorna 0.
        }

        // Verifica si la matrícula ya tiene un cálculo realizado.
        $exists = self::alreadyExists($tuitionId);

        if ($exists != -1) {
            return $exists; // Si ya existe, retorna el valor calculado previamente.
        }

        // Obtiene las operaciones relacionadas con la matrícula.
        $operations = Tuition::getTuitionAndOperationDetails($tuitionId, $workerTypeId, $schoolId);
        $act = 0;
        $inLiquidation = 0;
        if ($operations) {
            // Extrae los detalles de la clase con su operacion.
            $operationType = $operations->type;
            $operation = $operations->operation;
            $unitLimit = $operations->limit_unit;
            $minLimit = $operations->min_limit;
            $maxLimit = $operations->max_limit;
            $maxValueLimit = $operations->max_value;
            $months = $operations->application;
            $inLiquidation = $operations->in_liquidation;
            $workerType = $operations->worker_type;
            // Si el tipo de trabajador coincide o no está especificado, y la aplicación es válida, procesa la operación.
            if ((($workerTypeId == $workerType) || ($workerType == "")) && (self::checkApplication($months) == true)) {
                switch ($operationType) {
                    case "O": // Operación matemática (suma, resta, multiplicación, etc.)
                        $operation = self::removeExcessSpaces($operation); // Elimina espacios innecesarios.
                        $parts = explode(" ", $operation); // Separa la operación en partes.
                        $mem = 0;
                        $act = 0;
                        $op = "";
                        foreach ($parts as $item) {
                            // Ejecuta la operación matemática según el operador encontrado.
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
                                        if ($item == 0) {$act = 0;} else { $act = $act / $item;
                                            $op = "";}
                                        break;
                                    default:
                                        $act = $item;
                                        $op = "";
                                }
                            } elseif (($item == "*") || ($item == "/") || ($item == "+") || ($item == "-")) {
                                $op = $item;
                            } elseif (($item == "M+") || ($item == "M-") || ($item == "MR") || ($item == "MC")) {
                                if ($item == "M+") {$mem = $mem + $act;}
                                if ($item == "M-") {
                                    $mem = $mem - $act;
                                }
                                if ($item == "MR") {$act = $mem;}
                                if ($item == "MC") {
                                    $mem = 0;
                                }
                            } else {
                                // Si el ítem es otra clase se realiza un llamado recursivo.
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
                        // Aplica los límites y retorna el valor calculado.
                        $act = round(self::checkLimits($act, $unitLimit, $minLimit, $maxLimit, $maxValueLimit));
                        break;

                    case "P": // Parámetro directo
                        $act = Parameter::getParameterValue($tuitionId, $workerId, $schoolId); // Obtiene el valor del parámetro.
                        $unitParam = Parameter::getUnitByTuitionWorkerAndSchool($tuitionId, $workerId, $schoolId);
                        if ($unitParam != "") {
                            // Si hay unidad asociada, ajusta el valor según la unidad.
                            $unitValue = Parameter::getParameterValue($unitParam, $workerId, $schoolId);
                            $act = $act * $unitValue;
                        }
                        break;

                    case "S": // Suma de Parámetro
                        if ($tuitionId != "SUMACARGASTODOS") {
                            $act = Parameter::getSumValueByTuitionSchoolAndWorkerType($operation, $schoolId, $workerTypeId);
                        } else {
                            // Si es "SUMACARGASTODOS", suma los valores para todos los trabajadores.
                            $act = Parameter::getSumValueByTuitionSchoolAndWorkerType($operation, $schoolId, 1)
                             + Parameter::getSumValueByTuitionSchoolAndWorkerType($operation, $schoolId, 2);
                        }
                        break;
                }
            }
        }
        // Casos especiales, como la "Asignación Familiar".
        if ($tuitionId == "ASIGNACIONFAMILIAR") {
            if (0.8333 >= Parameter::getParameterValue("FACTORASIST", $workerId, $schoolId)) {
                $act = round($act * Parameter::getParameterValue("FACTORASIST", $workerId, $schoolId) / 0.8333);
            }
        }

        // Retorna el título asociado al parámetro y realiza ajustes según AFP o ISAPRE.
        $title = Parameter::getTitleByParameter($tuitionId, $workerId, $schoolId);

        if ($tuitionId == "AFP") {
            $title = Parameter::getDescriptionByCode("AFPTRABAJADOR", $workerId, $schoolId);
            $title = "(" . Parameter::getParameterValue("COTIZACIONAFP", $workerId, $schoolId) . " %) " . Insurance::getNameInsurance($title);
        }
        if ($tuitionId == "SALUD") {
            $title = Parameter::getDescriptionByCode("ISAPRETRABAJADOR", $workerId, $schoolId);
            $title = "(" . Parameter::getParameterValue("COTIZACIONISAPRE", $workerId, $schoolId) . " %) " . Insurance::getNameInsurance($title);
        }

        self::saveInTemporaryTable($tuitionId, $title, $act, $inLiquidation);

        return $act; // Retorna el valor final calculado.
    }

    /**
     * Guarda un cálculo en la "tabla temporal" (en la sesión).
     *
     * @param string $tuition_id El ID del cálculo.
     * @param string $title      El título del cálculo.
     * @param float  $value      El valor del cálculo.
     * @param int    $inLiquidation Indica si está en liquidación (1 o 0).
     */
    public static function saveInTemporaryTable($id, $title, $value, $inLiquidation)
    {
        // Verifica si el tuition_id ya existe en la tabla temporal 'tmp_liquidation'.
        $exists = TmpLiquidation::where('tuition_id', $id)->exists();

        // Si no existe, inserta un nuevo registro con los datos proporcionados.
        if (!$exists) {
            TmpLiquidation::create([
                'tuition_id' => $id, // Asigna el ID del cálculo.
                'title' => $title, // Asigna el título del cálculo.
                'value' => $value, // Asigna el valor del cálculo.
                'in_liquidation' => $inLiquidation, // Indica si está en liquidación.
            ]);
        }
    }

    /**
     * Recupera un cálculo desde la "tabla temporal" usando su ID.
     *
     * @param string $id El ID del cálculo.
     * @return mixed El valor del cálculo o null si no existe.
     */
    public static function getFromTemporaryTable($id)
    {
        // Busca el cálculo en la tabla 'tmp_liquidation' usando el 'tuition_id'.
        $record = TmpLiquidation::where('tuition_id', $id)->first();
        // Si se encuentra el cálculo, devuelve el valor, de lo contrario retorna null.
        return $record ? $record->value : null;
    }

    /**
     * Verifica si un cálculo ya existe en la tabla temporal.
     *
     * @param string $id El ID del cálculo.
     * @return mixed El valor del cálculo si existe, o -1 si no existe.
     */
    public static function alreadyExists($id)
    {
        // Busca si existe un registro en la tabla 'tmp_liquidation' con el 'tuition_id'.
        $record = TmpLiquidation::where('tuition_id', $id)->first();
        // Si el cálculo existe, retorna su valor; si no, retorna -1.
        return $record ? $record->value : -1;
    }

}
