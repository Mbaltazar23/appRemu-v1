<?php

namespace App\Helpers;

use App\Models\Insurance;
use App\Models\Operation;
use App\Models\Parameter;
use App\Models\Tuition;
use App\Models\Worker;
use Illuminate\Support\Facades\Session;

class CalculateLiquidation
{

    // Check limits. If the value exceeds the limit, return the limit. If below the limit, return 0.
    public function checkLimits($value, $tuitionId, $schoolId, $min, $max, $maxValue)
    {
        $unitValue = Parameter::where('name', $tuitionId)->where('school_id', $schoolId)->value('value');
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
    public static function checkApplication($months)
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

    public static function procesingCalculate($workerId, $workerType, $schoolId)
    {

        if ($workerType == Worker::WORKER_TYPE_NON_TEACHER) {
            $operation = Tuition::getOperationsByTuitionAndWorkerType('Sueldo Base', $workerType, $schoolId);
            if ($operation['title'] == 'Sueldo Base' && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);
                $value = Parameter::getParameterValue($parts[0], $workerId, $schoolId) * Parameter::getParameterValue($parts[2], $workerId, $schoolId);
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }
        }
        // Primero procesamos "Asignacion Voluntaria"
        $operation = Tuition::getOperationsByTuitionAndWorkerType("Asignacion Voluntaria", $workerType, $schoolId);

        if ($operation['title'] == "Asignacion Voluntaria" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);

            $titleParam = Parameter::where('name', $parts[0])->where('worker_id', $workerId)->value('value') ?? 0;
            $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
            $factorasist = Parameter::where('name', $parts[6])->where('worker_id', $workerId)->value('value');
            $value = $titleParam * (double) $parts[2] * $applicable * $factorasist;
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        if ($workerType == Worker::WORKER_TYPE_NON_TEACHER) {

            $operation = Tuition::getOperationsByTuitionAndWorkerType("Otros", $workerType, $schoolId);

            if ($operation['title'] == "Otros" && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);
                $titleParam = Parameter::where('name', $parts[0])->where('worker_id', $workerId)->value('value') ?? 0;
                $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
                $value = $titleParam * (double) $parts[2] * $applicable;
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }
        }

        if ($workerType == Worker::WORKER_TYPE_TEACHER) {

            $operation = Tuition::getOperationsByTuitionAndWorkerType("Perfeccionamiento", $workerType, $schoolId);

            if ($operation['title'] == "Perfeccionamiento" && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);

                $titleParam = Parameter::where('name', $parts[0])->where('worker_id', $workerId)->value('value') ?? 0;
                $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
                $factorasist = Parameter::where('name', $parts[6])->where('worker_id', $workerId)->value('value');
                $value = $titleParam * (double) $parts[2] * $applicable * $factorasist;
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }

            $operation = Tuition::getOperationsByTuitionAndWorkerType("RBMN", $workerType, $schoolId);

            if ($operation['title'] == "RBMN" && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);
                $titleParam = Parameter::where('name', $parts[0])->value('value');
                $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
                $loadhourly = Parameter::where('name', $parts[6])->where('worker_id', $workerId)->value('value');
                $factorasist = Parameter::where('name', $parts[8])->where('worker_id', $workerId)->value('value');

                $value = $titleParam * (double) $parts[2] * $applicable * $loadhourly * $factorasist;
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }

            $operation = Tuition::getOperationsByTuitionAndWorkerType("Ley 19410", $workerType, $schoolId);

            if ($operation['title'] == "Ley 19410" && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);
                $titleParam = Parameter::where('name', $parts[0])->value('value');
                $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
                $loadhourly = Parameter::where('name', $parts[6])->where('worker_id', $workerId)->value('value');
                $factorasist = Parameter::where('name', "FACTORASIST")->where('worker_id', $workerId)->value('value');
                $valLoad = 0;
                if ($parts[8] == "SUMACARGAS") {
                    $valLoad = Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[6], $schoolId, $workerType);
                }
                $value = ceil($titleParam * (double) $parts[2] * $applicable * $loadhourly / $valLoad * $factorasist);
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }

            $operation = Tuition::getOperationsByTuitionAndWorkerType("Ley 19933", $workerType, $schoolId);

            if ($operation['title'] == "Ley 19933" && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);
                $titleParam = Parameter::where('name', $parts[0])->value('value');
                $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
                $loadhourly = Parameter::where('name', $parts[6])->where('worker_id', $workerId)->value('value');
                $factorasist = Parameter::where('name', 'FACTORASIST')->where('worker_id', $workerId)->value('value');

                $valLoad = 0;
                if ($parts[8] == "SUMACARGAS") {
                    $valLoad = Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[6], $schoolId, $workerType);
                }
                $value = ceil($titleParam * (double) $parts[2] * $applicable * $loadhourly / $valLoad * $factorasist);
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }

            $operation = Tuition::getOperationsByTuitionAndWorkerType("UMP", $workerType, $schoolId);

            if ($operation['title'] == "UMP" && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);
                //dd($parts);
                $titleParam = Parameter::where('name', $parts[0])->value('value');
                $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
                $factorasist = Parameter::where('name', $parts[8])->where('worker_id', $workerId)->value('value');
                $valLoad = 0;
                if ($parts[6] == "TOPE30HORAS") {
                    $operationTope = Operation::getOperationFunction($parts[6], $workerType, $schoolId);
                    $valLoad = Parameter::where('name', $operationTope)->where('worker_id', $workerId)->value('value');
                }
                $value = $titleParam * (double) $parts[2] * $applicable * $valLoad * $factorasist;
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }

            $operation = Tuition::getOperationsByTuitionAndWorkerType("Imponible e imputable a la RTMN", $workerType, $schoolId);

            if ($operation['title'] == "Imponible e imputable a la RTMN" && self::checkApplication($operation['application'])) {
                // Paso 1: Dividir la cadena de operación en varios identificadores
                $ids = explode(" + ", $operation["operation"]);
                // Paso 2: Obtener los valores correspondientes a cada ID desde la sesión y sumarlos
                $totalValue = array_reduce($ids, function ($carry, $id) {
                    // Obtener el valor de la sesión usando el ID
                    $value = self::getFromTemporaryTable($id);
                    // Sumar el valor al acumulado (carry) si el valor existe
                    return $carry + ($value ? $value : 0); // Asegúrate de manejar valores nulos o no definidos
                }, 0); // Inicializamos el acumulador en 0
                // Paso 3: Guardar el resultado total en la variable 'value'
                $value = $totalValue;
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);

            }

            $operation = Tuition::getOperationsByTuitionAndWorkerType("Planilla complementaria", $workerType, $schoolId);

            if ($operation['title'] == "Planilla complementaria" && self::checkApplication($operation['application'])) {

                $parts = explode(" ", $operation["operation"]);
                $IMDParam = Parameter::where('name', $parts[0])->value('value');
                $loadhourly = Parameter::where('name', $parts[7])->where('worker_id', $workerId)->value('value');
                $factorasist = Parameter::where('name', "FACTORASIST")->where('worker_id', $workerId)->value('value');
            
                $valLoad = 0;
            
                if ($parts[2] == "SUMACARGAS") {
                    $valLoad = Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[7], $schoolId, $workerType);
                }
            
                // Verificamos que $valLoad no sea cero antes de realizar la división
                if ($valLoad != 0) {
                    // Multiplicamos por CARGAHORARIA y FACTORASIST
                    $finalResult = ceil(($IMDParam / $valLoad) * $loadhourly * $factorasist);
                } else {
                    // Si $valLoad es cero, manejamos el caso, por ejemplo asignando un valor predeterminado
                    $finalResult = 0; // O cualquier otro valor que sea apropiado
                }            
                // Paso 6: Guardar el resultado final en la tabla temporal
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $finalResult, $operation['in_liquidation']);
            }
            

            $operation = Tuition::getOperationsByTuitionAndWorkerType("R.T.M.N", $workerType, $schoolId);

            if ($operation['title'] == "R.T.M.N" && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);
                $value = self::getFromTemporaryTable($parts[0]) + self::getFromTemporaryTable($parts[2]);
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }

            $operation = Tuition::getOperationsByTuitionAndWorkerType("Imponible y No imputable a la R.T.M.N.", $workerType, $schoolId);
            if ($operation['title'] == "Imponible y No imputable a la R.T.M.N." && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);
                if ($parts[0] == "HORASPERFECCIONAMIENTO") {
                    $parameter = Parameter::getParametersWithTuitions($parts[0], $schoolId, $workerId);
                    self::saveInTemporaryTable($parameter['tuition_id'], $parameter['title'], ceil($parameter['value']), $parameter['in_liquidation']);
                }
                $parameterHourly = Parameter::where('name', $parts[0])->value('value');
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], ceil($parameterHourly), $operation['in_liquidation']);
            }

        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType("Desempeño dificil", $workerType, $schoolId);

        if ($operation['title'] == "Desempeño dificil" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $titleParam = Parameter::where('name', $parts[0])->value('value');
            $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value');
            $loadhourly = Parameter::where('name', $parts[6])->where('worker_id', $workerId)->value('value') ?? 0;
            $factorasist = Parameter::where('name', $parts[8])->where('worker_id', $workerId)->value('value');
            $act = 0;
            $value = 0;
            if ($parts[8] == "SUMACARGASTODOS") {
                $sumParam = Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[0], $schoolId, 1) + Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[0], $schoolId, 2);
                $sumAppli = Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[4], $schoolId, 1) + Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[4], $schoolId, 2);
                $sumLoad = Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[6], $schoolId, 1) + Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[6], $schoolId, 2);
                $act = $sumParam + $sumAppli + $sumLoad;
            }
            // Realizar el cálculo si 'act' no es cero
            $value = ceil($titleParam * (double) $parts[2] * $applicable * $loadhourly / $act * $factorasist);

            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        if ($workerType == Worker::WORKER_TYPE_NON_TEACHER) {
            $operation = Tuition::getOperationsByTuitionAndWorkerType("Ley 19464", $workerType, $schoolId);

            if ($operation['title'] == "Ley 19464" && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);
                $titleParam = Parameter::where('name', $parts[0])->value('value');
                $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
                $loadhourly = Parameter::where('name', $parts[6])->where('worker_id', $workerId)->value('value');
                $factorasist = Parameter::where('name', "FACTORASIST")->where('worker_id', $workerId)->value('value');
                $valLoad = 0;
                if ($parts[8] == "SUMACARGAS") {
                    $valLoad = Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[6], $schoolId, $workerType);
                }
                $value = ceil($titleParam * (double) $parts[2] * $applicable * $loadhourly / $valLoad * $factorasist);
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType("Renta Imponible", $workerType, $schoolId);
        if ($operation['title'] == "Renta Imponible" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $operationRent = Operation::where('tuition_id', $parts[0])->where('worker_type', $workerType)->first();
            if ($operationRent) {
                $partsOp = explode(" ", $operationRent['operation']);
                $value = self::getFromTemporaryTable($partsOp[0]) + self::getFromTemporaryTable($partsOp[1]) + self::getFromTemporaryTable($partsOp[2]) + self::getFromTemporaryTable($partsOp[3]);
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType("Asignacion familiar", 2, $schoolId);

        if ($operation['title'] == "Asignacion familiar" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            //dd($parts);
            $filtroAsig1 = Parameter::where('name', $parts[0])->where('school_id', $schoolId)->value('value');
            $AsingAsig1 = Parameter::where('name', $parts[4])->where('school_id', $schoolId)->value('value');
            $filtroAsig2 = Parameter::where('name', $parts[7])->where('school_id', $schoolId)->value('value');
            $AsingAsig2 = Parameter::where('name', $parts[11])->where('school_id', $schoolId)->value('value');
            $filtroAsig3 = Parameter::where('name', $parts[14])->where('school_id', $schoolId)->value('value');
            $AsingAsig3 = Parameter::where('name', $parts[18])->where('school_id', $schoolId)->value('value');
            $loadFamilys = Parameter::where('name', $parts[23])->where('worker_id', $workerId)->value('value');

            $value = ($filtroAsig1 / $filtroAsig1 * $AsingAsig1) + ($filtroAsig2 / $filtroAsig2 * $AsingAsig2) + ($filtroAsig3 / $filtroAsig3 * $AsingAsig3) * $loadFamilys;

            if ($operation['title'] == "Asignacion familiar") {
                if (0.8333 >= Parameter::getParameterValue("FACTORASIST", $workerId, $schoolId) && Parameter::getParameterValue($parts[23], $workerId, $schoolId) > 0) {
                    $value = round($value * Parameter::getParameterValue("FACTORASIST", $workerId, $schoolId) / 0.8333);
                }
            }
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        if ($workerType == Worker::WORKER_TYPE_TEACHER) {

            $operation = Tuition::getOperationsByTuitionAndWorkerType("Excedente Bono SAE ley 19410/19933", $workerType, $schoolId);

            if ($operation['title'] == "Excedente Bono SAE ley 19410/19933") {
                $parts = explode(" ", $operation["operation"]);
                $loadhourly = Parameter::where('name', $parts[10])->where('worker_id', $workerId)->value('value');
                $valLoad = 0;
                if ($parts[12] == "SUMACARGAS") {
                    $valLoad = Parameter::getSumValueByTuitionSchoolAndWorkerType($parts[10], $schoolId, $workerType);
                }

                $value = self::checkApplication($operation['application']) ? ceil(self::getFromTemporaryTable($parts[0]) + self::getFromTemporaryTable($parts[2]) / (double) $parts[4] * (double) $parts[6] * (double) $parts[8] * $loadhourly / $valLoad) : 0;

                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Total no imponible', 1, $schoolId);

        if ($operation['title'] == 'Total no imponible' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $value = self::getFromTemporaryTable($parts[0]) + self::getFromTemporaryTable($parts[2]);
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Total Haberes', $workerType, $schoolId);

        if ($operation['title'] == 'Total Haberes' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $value = self::getFromTemporaryTable($parts[0]) + self::getFromTemporaryTable($parts[2]);
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        // Ahora procesamos "AFP"
        $operation = Tuition::getOperationsByTuitionAndWorkerType("AFP", 1, $schoolId);

        $title = Parameter::getTitleByParameter($operation['tuition_id'], $workerId, $schoolId);

        if ($operation["tuition_id"] == "AFP" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $title = Parameter::getDescriptionByCode("AFPTRABAJADOR", $workerId, $schoolId);
            $titleAfp = "(" . Parameter::getParameterValue("COTIZACIONAFP", $workerId, $schoolId) . " %) " . Insurance::getNameInsurance($title);
            $operation['title'] = $titleAfp;
            $value = ceil(Parameter::where('name', $parts[0])->where('worker_id', $workerId)->value('value') / (double) $parts[2] * self::getFromTemporaryTable($parts[4]));
            // Guardamos esta operación sin sobrescribir las anteriores
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }
        // Finalmente procesamos "SALUD"
        $operation = Tuition::getOperationsByTuitionAndWorkerType("SALUD", 1, $schoolId);

        if ($operation["tuition_id"] == "SALUD" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $title = Parameter::getDescriptionByCode("ISAPRETRABAJADOR", $workerId, $schoolId);
            $titleSalud = "(" . Parameter::getParameterValue("COTIZACIONISAPRE", $workerId, $schoolId) . " %) " . Insurance::getNameInsurance($title);
            $operation['title'] = $titleSalud;
            $value = ceil(Parameter::where('name', $parts[0])->where('worker_id', $workerId)->value('value') * self::getFromTemporaryTable($parts[2]) / (double) $parts[4]);
            // Guardamos esta operación sin sobrescribir las anteriores
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Seguro cesantia', 2, $schoolId);

        if ($operation['title'] == 'Seguro cesantia' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $filtroContracIndDef = Operation::getOperationFunction($parts[0], 2, $schoolId); //para ambos casos el valor de ambas operaciones es igual
            $valuefilter = 0;
            if ($filtroContracIndDef) {
                $partsContract = explode(" ", $filtroContracIndDef);
                $valuefilter = (int) $partsContract[0] - Parameter::getParameterValue($partsContract[2], $workerId, $schoolId) * Parameter::getParameterValue($partsContract[4], $workerId, $schoolId);

                if ($valuefilter < 0) { // Verifica si $valuefilter es negativo
                    $value = ceil($valuefilter / $valuefilter * (double) $parts[4] * self::getFromTemporaryTable($parts[6])
                         + $valuefilter / $valuefilter * (double) $parts[13] * self::getFromTemporaryTable($parts[15]));
                } else {
                    $value = 0;
                }
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
            }
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Descuentos previsionales', 1, $schoolId);

        if ($operation['title'] == 'Descuentos previsionales' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $value = self::getFromTemporaryTable($parts[0]) + self::getFromTemporaryTable($parts[2]) + self::getFromTemporaryTable($parts[4]);
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Adicional de salud', $workerType, $schoolId);

        if ($operation['title'] == 'Adicional de salud' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);

            // Comprobamos si el valor de $parts[2] es mayor a 1
            if ($parts[2] > $parts[0]) {
                $value = self::getFromTemporaryTable($parts[2]) - Parameter::getParameterValue($parts[0], $workerId, $schoolId);
            } else {
                $value = Parameter::getParameterValue($parts[0], $workerId, $schoolId) - self::getFromTemporaryTable($parts[2]);
            }
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Descuentos voluntarios', 1, $schoolId);

        if ($operation['title'] == "Descuentos voluntarios" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            if ($parts[2] == "APV") {
                $parameter = Parameter::getParametersWithTuitions($parts[2], $schoolId, $workerId);
                self::saveInTemporaryTable($parameter['tuition_id'], $parameter['title'], ceil($parameter['value']), $parameter['in_liquidation']);
            }
            if ($parts[4] == "AFPOTRO") {
                $parameter = Parameter::getParametersWithTuitions($parts[4], $schoolId, $workerId);
                self::saveInTemporaryTable($parameter['tuition_id'], $parameter['title'], ceil($parameter['value']), $parameter['in_liquidation']);
            }
            if ($parts[6] == "ISAPREOTRO") {
                $parameter = Parameter::getParametersWithTuitions($parts[6], $schoolId, $workerId);
                self::saveInTemporaryTable($parameter['tuition_id'], $parameter['title'], ceil($parameter['value']), $parameter['in_liquidation']);
            }
        }

        if ($workerType == Worker::WORKER_TYPE_TEACHER) {

            $operation = Tuition::getOperationsByTuitionAndWorkerType("Colegio de profesores", $workerType, $schoolId);

            if ($operation['title'] == "Colegio de profesores" && self::checkApplication($operation['application'])) {
                $parts = explode(" ", $operation["operation"]);

                $rentaimponibleSDOP = Operation::getOperationFunction($parts[2], $workerType, $schoolId);
                $valueRent = 0;
                if ($rentaimponibleSDOP) {
                    $operations = explode(" ", $rentaimponibleSDOP);
                    // Filtrar el array para eliminar los elementos que contienen '+'
                    $operations = array_filter($operations, function ($item) {
                        return strpos($item, '+') === false; // Solo conserva los elementos que NO contienen '+'
                    });
                    $operations = array_values($operations);
                    foreach ($operations as $op) {
                        $valueRent += self::getFromTemporaryTable($op);
                    }
                    $value = ceil((double) $parts[0] * $valueRent * Parameter::getParameterValue($parts[4], $workerId, $schoolId));
                    self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
                }
            }
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType("Fundacion Lopez Perez", $workerType, $schoolId);

        if ($operation['title'] == "Fundacion Lopez Perez" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $titleParam = Parameter::where('name', $parts[0])->where('worker_id', $workerId)->value('value') ?? 0;
            $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
            $value = $titleParam * (double) $parts[2] * $applicable;
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType("Prestamo Social Caja los Andes", $workerType, $schoolId);

        if ($operation['title'] == "Prestamo Social Caja los Andes" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $titleParam = Parameter::where('name', $parts[0])->where('worker_id', $workerId)->value('value') ?? 0;
            $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
            $value = $titleParam * (double) $parts[2] * $applicable;
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType("Prestamo Social Caja los heroes", $workerType, $schoolId);

        if ($operation['title'] == "Prestamo Social Caja los heroes" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);

            $titleParam = Parameter::where('name', $parts[0])->where('worker_id', $workerId)->value('value') ?? 0;
            $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
            $factorasist = Parameter::where('name', $parts[6])->where('worker_id', $workerId)->value('value');
            $value = $titleParam * (double) $parts[2] * $applicable * $factorasist;
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType("Cuenta Ahorro Caja los Andes", $workerType, $schoolId);

        if ($operation['title'] == "Cuenta Ahorro Caja los Andes" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $titleParam = Parameter::where('name', $parts[0])->where('worker_id', $workerId)->value('value') ?? 0;
            $applicable = Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') ?? 0;
            $value = $titleParam * (double) $parts[2] * $applicable;
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Descuentos voluntarios', 1, $schoolId);

        if ($operation['title'] == "Descuentos voluntarios" && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $value = ceil(Parameter::where('name', $parts[0])->where('worker_id', $workerId)->value('value') + Parameter::where('name', $parts[2])->where('worker_id', $workerId)->value('value') + Parameter::where('name', $parts[4])->where('worker_id', $workerId)->value('value') + Parameter::where('name', $parts[6])->where('worker_id', $workerId)->value('value') + self::getFromTemporaryTable($parts[8]) + self::getFromTemporaryTable($parts[10]) + self::getFromTemporaryTable($parts[12]));
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Remuneración tributable', 1, $schoolId);

        if ($operation['title'] == 'Remuneración tributable' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $value = self::getFromTemporaryTable($parts[0]) - self::getFromTemporaryTable($parts[2]);
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Impuesto Único', 1, $schoolId);

        if ($operation['title'] == 'Impuesto Único' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);

            // Obtener la operación del impuesto (IMPUESTOTRAMO2, IMPUESTOTRAMO3, ...)
            $impuesto2 = Operation::where('tuition_id', $parts[0])->where('school_id', $schoolId)->value('operation');
            $operationImp = self::getFromTemporaryTable($impuesto2); // Esto es la REMUNERACIÓN TRIBUTABLE
            // Obtener los factores de descuento de cada tramo (por ejemplo, FACTORREBAJAIMPTRAMO2, FACTORREBAJAIMPTRAMO3, etc.)
            $factorImp2 = Parameter::where('name', $parts[4])->where('school_id', $schoolId)->value('value');
            $factorImp3 = Parameter::where('name', $parts[11])->where('school_id', $schoolId)->value('value');
            $factorImp4 = Parameter::where('name', $parts[18])->where('school_id', $schoolId)->value('value');
            $factorImp5 = Parameter::where('name', $parts[25])->where('school_id', $schoolId)->value('value');
            $factorImp6 = Parameter::where('name', $parts[32])->where('school_id', $schoolId)->value('value');
            $factorImp7 = Parameter::where('name', $parts[39])->where('school_id', $schoolId)->value('value');
            $factorImp8 = Parameter::where('name', $parts[46])->where('school_id', $schoolId)->value('value');

            // Obtener la remuneración tributaria
            $remuneracionTributable = self::getFromTemporaryTable($parts[51]);

            // Los factores deben ser porcentajes, así que los dividimos entre 100
            $impuestoTotal =
                ($remuneracionTributable * ($factorImp2 / 100)) +
                ($remuneracionTributable * ($factorImp3 / 100)) +
                ($remuneracionTributable * ($factorImp4 / 100)) +
                ($remuneracionTributable * ($factorImp5 / 100)) +
                ($remuneracionTributable * ($factorImp6 / 100)) +
                ($remuneracionTributable * ($factorImp7 / 100)) +
                ($remuneracionTributable * ($factorImp8 / 100));

            // Finalmente, si es necesario, guardamos el resultado en la tabla temporal
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $impuestoTotal, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Rebaja Impuesto', 2, $schoolId);

        if ($operation['title'] == 'Rebaja Impuesto' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);

            // Obtenemos el valor de $operationImp
            $impuesto2 = Operation::where('tuition_id', $parts[0])->where('school_id', $schoolId)->value('operation');
            $operationImp = self::getFromTemporaryTable($impuesto2);

            // Comprobamos si $operationImp es cero
            if ($operationImp != 0) {
                // Obtenemos los factores
                $factorImp2 = Parameter::where('name', $parts[4])->where('school_id', $schoolId)->value('value');
                $factorImp3 = Parameter::where('name', $parts[11])->where('school_id', $schoolId)->value('value');
                $factorImp4 = Parameter::where('name', $parts[18])->where('school_id', $schoolId)->value('value');
                $factorImp5 = Parameter::where('name', $parts[25])->where('school_id', $schoolId)->value('value');
                $factorImp6 = Parameter::where('name', $parts[32])->where('school_id', $schoolId)->value('value');
                $factorImp7 = Parameter::where('name', $parts[39])->where('school_id', $schoolId)->value('value');
                $factorImp8 = Parameter::where('name', $parts[46])->where('school_id', $schoolId)->value('value');

                // Calculamos el valor
                $value = ceil(($operationImp / $operationImp * $factorImp2) +
                    ($operationImp / $operationImp * $factorImp3) +
                    ($operationImp / $operationImp * $factorImp4) +
                    ($operationImp / $operationImp * $factorImp5) +
                    ($operationImp / $operationImp * $factorImp6) +
                    ($operationImp / $operationImp * $factorImp7) +
                    ($operationImp / $operationImp * $factorImp8));

                // Guardamos el resultado
                self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value ?? 0, $operation['in_liquidation']);
            }
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Impuesto Renta', 1, $schoolId);

        if ($operation['title'] == 'Impuesto Renta' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $value = self::getFromTemporaryTable($parts[0]) - self::getFromTemporaryTable($parts[2]);
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Total Descuentos', $workerType, $schoolId);

        if ($operation['title'] == 'Total Descuentos' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $value = self::getFromTemporaryTable($parts[0]) + self::getFromTemporaryTable($parts[2]) + self::getFromTemporaryTable($parts[4]);
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

        $operation = Tuition::getOperationsByTuitionAndWorkerType('Total a Pagar', 1, $schoolId);

        if ($operation['title'] == 'Total a Pagar' && self::checkApplication($operation['application'])) {
            $parts = explode(" ", $operation["operation"]);
            $value = self::getFromTemporaryTable($parts[0]) - self::getFromTemporaryTable($parts[2]);
            self::saveInTemporaryTable($operation['tuition_id'], $operation['title'], $value, $operation['in_liquidation']);
        }

    }

    /**
     * Create a "temporary table" in the session to store liquidation calculations.
     * The "table" is simply an array stored in the session.
     */
    public static function createTemporaryTable()
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
    public static function saveInTemporaryTable($id, $title, $value, $inLiquidation)
    {
        // Retrieve the "temporary table" from the session
        $tmp = Session::get('tmp');
        // Creamos el objeto LiquidationRecord
        $record = new LiquidationRecord($id, $title, $value, $inLiquidation);
        // Guardamos el objeto en la sesión con el ID como clave
        $tmp[$id] = $record;
        // Guardamos la "tabla temporal" de vuelta en la sesión
        Session::put('tmp', $tmp);
    }
    /**
     * Retrieve a calculation from the "temporary table" by its ID.
     *
     * @param string $id The ID of the calculation
     * @return mixed The calculation or null if it does not exist
     */
    public static function getFromTemporaryTable($id)
    {
        // Retrieve the "temporary table" from the session
        $tmp = Session::get('tmp', []);

        // If the ID exists, return it
        return isset($tmp[$id]) ? $tmp[$id]->value : null;
    }
    public function alreadyExists($id)
    {
        // Recupera los datos de la "tabla temporal" desde la sesión
        $tmp = Session::get('tmp', []);
        // Verifica si el ID existe en el arreglo de la sesión
        if (isset($tmp[$id])) {
            // Retorna el campo [2] (que es el value del LiquidationRecord)
            return $tmp[$id]->value; // Accediendo al campo value del objeto LiquidationRecord
        }
        // Si no existe, retorna false
        return false;
    }

}
