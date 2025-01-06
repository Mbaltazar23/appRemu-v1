<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    // Atributos que pueden ser asignados masivamente
    protected $fillable = [
        'tuition_id',
        'school_id',
        'worker_type',
        'operation',
        'limit_unit',
        'min_limit',
        'max_limit',
        'max_value',
        'application',
    ];

    const WORKER_TYPE_TEACHER = 1;
    const WORKER_TYPE_NON_TEACHER = 2;
    const WORKER_TYPE_ALL = 3;

    public static function getWorkerTypes()
    {
        return [
            self::WORKER_TYPE_TEACHER => 'Docente',
            self::WORKER_TYPE_NON_TEACHER => 'No Docente',
            self::WORKER_TYPE_ALL => "Todos",
        ];
    }

    public static function generateOperation($data, $nombre, $nombrev, $factor)
    {
        switch ($data['application']) {
            case "H":
                return $data['type'] != 3 ?
                "{$nombrev} * {$factor} * APLICA{$nombre} * CARGAHORARIA / SUMACARGAS" :
                "{$nombrev} * {$factor} * APLICA{$nombre} * CARGAHORARIA / SUMACARGASTODOS";
            case "C":
                return "{$nombrev} * {$factor} * APLICA{$nombre} * CARGAHORARIA";
            case "T":
                return "{$nombrev} * {$factor} * APLICA{$nombre} * TOPE30HORAS";
            case "D":
                return "{$nombrev} * {$factor} * APLICA{$nombre}";
            case "F":
                return "{$nombrev} * {$factor} * APLICA{$nombre}";
            case "I":
                return "{$factor} * RENTAIMPONIBLESD * APLICA{$nombre}";
        }
    }

    public static function addOperation($tuitionId, $workerType, $operation, $application, $school_id)
    {
        self::create([
            'tuition_id' => $tuitionId,
            'school_id' => $school_id,
            'worker_type' => $workerType,
            'operation' => $operation,
            'min_limit' => 0,
            'max_limit' => 0,
            'max_value' => 0,
            'application' => $application,
        ]);
    }

    public static function getOperationFunction($tuitionId, $workerType, $schoolId)
    {
        return self::where('tuition_id', $tuitionId)
            ->where('worker_type', $workerType)
            ->where('school_id', $schoolId)
            ->value('operation');
    }

    public static function updateOperationFunction($tuitionId, $workerType, $function, $schoolId)
    {
        // Realizar el update directamente usando where
        return self::where([
            ['tuition_id', '=', $tuitionId],
            ['worker_type', '=', $workerType],
            ['school_id', '=', $schoolId],
        ])
            ->update([
                'operation' => $function,
            ]);
    }

    public static function getOperasobreClase($data)
    {
        // Verificar si es un bono
        if ($data['is_bonus'] == 0) {
            // Verificar si es imponible
            if ($data['taxable'] == 0) {
                // Verificar si es imputable
                if ($data['imputable'] == 0) {
                    $operasobreclase = "IMPONIBLEEIMPUTABLE";
                } else {
                    // Verificar el tipo para decidir el nombre de la clase
                    if ($data['type'] == 1) {
                        $operasobreclase = "IMPONIBLEYNOIMPUTABLE";
                    } else {
                        $operasobreclase = "RENTAIMPONIBLESD";
                    }
                }
            } else {
                $operasobreclase = "TOTALNOIMPONIBLE";
            }
        } else {
            // Si no es bono
            $operasobreclase = "DESCUENTOSVOLUNTARIOS";
        }
        // Retornar el resultado final
        return $operasobreclase;
    }

    public static function deleteOperation($tuitionId, $workerType, $schoolId)
    {
        return self::where('tuition_id', $tuitionId)
            ->where('worker_type', $workerType)
            ->where('school_id', $schoolId)
            ->delete();
    }

    public static function processOperation($nombre, $data, $operation, $meses, $operador, $option)
    {
        $operasobreclase = self::getOperasobreClase($data);

        if (($operasobreclase == "RENTAIMPONIBLESD") || ($operasobreclase == "IMPONIBLEEIMPUTABLE") || ($operasobreclase == "IMPONIBLEYNOIMPUTABLE")) {
            $operation .= " * FACTORASIST";
        }

        // Agregar operación
        if ($option) {
            if ($data['type'] != 3) {
                self::addOperation($nombre, $data['type'], $operation, $meses, $data['school_id']);
            } else {
                self::addOperation($nombre, 1, $operation, $meses, $data['school_id']);
                self::addOperation($nombre, 2, $operation, $meses, $data['school_id']);
            }
        }

        // Actualizar operaciones originales
        if ($data['type'] != 3) {
            if (($data['type'] == 2) && ($operasobreclase == "IMPONIBLEEIMPUTABLE")) {
                $operasobreclase = "RENTAIMPONIBLESD";
            }

            $operationOriginal = self::getOperationFunction($operasobreclase, $data['type'], $data['school_id']);
            $operationOriginal = str_replace(" " . $operador . " " . $nombre, " ", $operationOriginal);
            $newOperation = $operationOriginal . " " . $operador . " " . $nombre;
            self::updateOperationFunction($operasobreclase, $data['type'], $newOperation, $data['school_id']);
        } else {
            // Para docentes
            $operationOriginal = self::getOperationFunction($operasobreclase, 1, $data['school_id']);
            $operationOriginal = str_replace(" " . $operador . " " . $nombre, " ", $operationOriginal);
            $newOperation = $operationOriginal . " " . $operador . " " . $nombre;

            self::updateOperationFunction($operasobreclase, 1, $newOperation, $data['school_id']);

            // Para no docentes
            if ($operasobreclase == "IMPONIBLEEIMPUTABLE") {
                $operasobreclase = "RENTAIMPONIBLESD";
            }

            $operationOriginal = self::getOperationFunction($operasobreclase, 2, $data['school_id']);
            // Solo eliminamos el operador y nombre si la operación original no está vacía
            $operationOriginal = str_replace(" " . $operador . " " . $nombre, " ", $operationOriginal);
            // Comprobar si la operación original no está vacía antes de añadir el operador
            $newOperation = $operationOriginal . " " . $operador . " " . $nombre;

            self::updateOperationFunction($operasobreclase, 2, $newOperation, $data['school_id']);
        }

    }

    public static function processDeleteOperation($nombre, $data, $operador, $meses)
    {
        $operasobreclase = self::getOperasobreClase($data);

        if ($data['type'] != 3) {
            $operationOriginal = self::getOperationFunction($operasobreclase, $data['type'], $data['school_id']);
            $operationOriginal = str_replace(" " . $operador . " " . $nombre, " ", $operationOriginal);
            $newOperation = $operationOriginal . " " . $operador . " " . $nombre;
            if (($data['type'] == 2) && ($operasobreclase == "IMPONIBLEEIMPUTABLE")) {
                $operasobreclase = "RENTAIMPONIBLESD";
            }
            self::updateOperationFunction($operasobreclase, $data['type'], $newOperation, $data['school_id'], $meses);
        } else {
            // Para docentes
            $operationOriginal = self::getOperationFunction($operasobreclase, 1, $data['school_id']);
            $operationOriginal = str_replace(" " . $operador . " " . $nombre, " ", $operationOriginal);
            $newOperation = $operationOriginal . " " . $operador . " " . $nombre;

            self::updateOperationFunction($operasobreclase, 1, $newOperation, $data['school_id']);

            // Para no docentes
            if ($operasobreclase == "IMPONIBLEEIMPUTABLE") {
                $operasobreclase = "RENTAIMPONIBLESD";
            }

            $operationOriginal = self::getOperationFunction($operasobreclase, 2, $data['school_id']);
            $operationOriginal = str_replace(" " . $operador . " " . $nombre, " ", $operationOriginal);
            $newOperation = $operationOriginal . " " . $operador . " " . $nombre;

            self::updateOperationFunction($operasobreclase, 2, $newOperation, $data['school_id']);
        }
    }

    public static function getMounthOperations($idclase, $tipotrabajador, $idcolegio)
    {
        return self::where('tuition_id', $idclase)
            ->where('worker_type', $tipotrabajador)
            ->where('school_id', $idcolegio)
            ->value('application');
    }

    // Método para obtener el límite mínimo del impuesto a la renta
    public static function getMinLimit($tuitionId)
    {
        return self::where('tuition_id', $tuitionId)->value('min_limit');
    }

    // Método para obtener el límite máximo del impuesto a la renta
    public static function getMaxLimit($tuitionId)
    {
        return self::where('tuition_id', $tuitionId)->value('max_limit');
    }

    // Método para obtener el valor del impuesto a la renta
    public static function getTaxValue($tuitionId)
    {
        return self::where('tuition_id', $tuitionId)->pluck('max_value')->first();
    }

    public static function updOrInsertTopesOperation($tuitionId, $min, $max)
    {
        $operation = self::where('tuition_id', $tuitionId)->first();

        if ($operation) {
            // Si existe, actualiza
            $operation->update(['min_limit' => $min, 'max_limit' => $max, 'updated_at'=> now()]);
        } else {
            // Si no existe, crea uno nuevo
            self::create([
                'tuition_id' => $tuitionId,
                'min_limit' => $min,
                'max_limit' => $max,
                'created_at' => now(), // Establece la fecha de creación
                'updated_at' => now(), // Establece la fecha de actualización
            ]);
        }
    }

    // Relación: Una Operation pertenece a una Tuition
    public function tuition()
    {
        return $this->belongsTo(Tuition::class);
    }

    // Relación: Una Operation pertenece a una School
    public function school()
    {
        return $this->belongsTo(School::class);
    }

}
