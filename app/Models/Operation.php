<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;
    // Attributes that can be mass-assigned
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
    // Constants for worker types
    const WORKER_TYPE_TEACHER     = 1;
    const WORKER_TYPE_NON_TEACHER = 2;
    const WORKER_TYPE_ALL         = 3;
    /**
     * Get the possible worker types.
     *
     * @return array
     */
    public static function getWorkerTypes()
    {
        return [
            self::WORKER_TYPE_TEACHER     => 'Docente',
            self::WORKER_TYPE_NON_TEACHER => 'No Docente',
            self::WORKER_TYPE_ALL         => "Todos",
        ];
    }
    /**
     * Generate an operation formula based on the provided application type.
     *
     * @param array $data
     * @param string $nombre
     * @param string $nombrev
     * @param float $factor
     * @return string
     */
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
    /**
     * Add a new operation to the database.
     *
     * @param int $tuitionId
     * @param int $workerType
     * @param string $operation
     * @param string $application
     * @param int $school_id
     */
    public static function addOperation($tuitionId, $workerType, $operation, $application, $school_id)
    {
        self::create([
            'tuition_id'  => $tuitionId,
            'school_id'   => $school_id,
            'worker_type' => $workerType,
            'operation'   => $operation,
            'min_limit'   => 0,
            'max_limit'   => 0,
            'max_value'   => 0,
            'application' => $application,
        ]);
    }
    /**
     * Retrieve the operation function for a specific tuition, worker type, and school.
     *
     * @param int $tuitionId
     * @param int $workerType
     * @param int $schoolId
     * @return string
     */
    public static function getOperationFunction($tuitionId, $workerType, $schoolId)
    {
        return self::where('tuition_id', $tuitionId)
            ->where('worker_type', $workerType)
            ->where('school_id', $schoolId)
            ->value('operation');
    }
    /**
     * Update the operation function for a specific tuition, worker type, and school.
     *
     * @param int $tuitionId
     * @param int $workerType
     * @param string $function
     * @param int $schoolId
     * @return int
     */
    public static function updateOperationFunction($tuitionId, $workerType, $function, $schoolId)
    {
        return self::where([
            ['tuition_id', '=', $tuitionId],
            ['worker_type', '=', $workerType],
            ['school_id', '=', $schoolId],
        ])
            ->update([
                'operation' => $function,
            ]);
    }
    /**
     * Get the class name for the operation based on various conditions.
     *
     * @param array $data
     * @return string
     */
    public static function getOperasobreClase($data)
    {
        if ($data['is_bonus'] == 0) {
            if ($data['taxable'] == 0) {
                if ($data['imputable'] == 0) {
                    $operasobreclase = "IMPONIBLEEIMPUTABLE";
                } else {
                    $operasobreclase = $data['type'] == 1 ? "IMPONIBLEYNOIMPUTABLE" : "RENTAIMPONIBLESD";
                }
            } else {
                $operasobreclase = "TOTALNOIMPONIBLE";
            }
        } else {
            $operasobreclase = "DESCUENTOSVOLUNTARIOS";
        }
        return $operasobreclase;
    }
    /**
     * Delete an operation for a specific tuition, worker type, and school.
     *
     * @param int $tuitionId
     * @param int $workerType
     * @param int $schoolId
     * @return int
     */
    public static function deleteOperation($tuitionId, $workerType, $schoolId)
    {
        return self::where('tuition_id', $tuitionId)
            ->where('worker_type', $workerType)
            ->where('school_id', $schoolId)
            ->delete();
    }
    /**
     * Process the operation based on the provided data, applying modifications as needed.
     *
     * @param string $nombre
     * @param array $data
     * @param string $operation
     * @param string $meses
     * @param string $operador
     * @param bool $option
     */
    public static function processOperation($nombre, $data, $operation, $meses, $operador, $option)
    {
        $operasobreclase = self::getOperasobreClase($data);

        if (in_array($operasobreclase, ["RENTAIMPONIBLESD", "IMPONIBLEEIMPUTABLE", "IMPONIBLEYNOIMPUTABLE"])) {
            $operation .= " * FACTORASIST";
        }

        if ($option) {
            if ($data['type'] != 3) {
                self::addOperation($nombre, $data['type'], $operation, $meses, $data['school_id']);
            } else {
                self::addOperation($nombre, 1, $operation, $meses, $data['school_id']);
                self::addOperation($nombre, 2, $operation, $meses, $data['school_id']);
            }
        }

        // Update original operations for teachers and non-teachers
        $operationOriginal = self::getOperationFunction($operasobreclase, $data['type'], $data['school_id']);
        $operationOriginal = str_replace(" " . $operador . " " . $nombre, " ", $operationOriginal);
        $newOperation      = $operationOriginal . " " . $operador . " " . $nombre;

        if ($data['type'] == 2 && $operasobreclase == "IMPONIBLEEIMPUTABLE") {
            $operasobreclase = "RENTAIMPONIBLESD";
        }

        self::updateOperationFunction($operasobreclase, $data['type'], $newOperation, $data['school_id']);
    }
    /**
     * Process and delete an operation based on the provided data.
     *
     * @param string $nombre
     * @param array $data
     * @param string $operador
     * @param string $meses
     */
    public static function processDeleteOperation($nombre, $data, $operador, $meses)
    {
        $operasobreclase   = self::getOperasobreClase($data);
        $operationOriginal = self::getOperationFunction($operasobreclase, $data['type'], $data['school_id']);
        $operationOriginal = str_replace(" " . $operador . " " . $nombre, " ", $operationOriginal);
        $newOperation      = $operationOriginal . " " . $operador . " " . $nombre;

        if ($data['type'] != 3) {
            if (($data['type'] == 2) && ($operasobreclase == "IMPONIBLEEIMPUTABLE")) {
                $operasobreclase = "RENTAIMPONIBLESD";
            }
            self::updateOperationFunction($operasobreclase, $data['type'], $newOperation, $data['school_id'], $meses);
        } else {
            self::updateOperationFunction($operasobreclase, 1, $newOperation, $data['school_id']);
            if ($operasobreclase == "IMPONIBLEEIMPUTABLE") {
                $operasobreclase = "RENTAIMPONIBLESD";
            }
            self::updateOperationFunction($operasobreclase, 2, $newOperation, $data['school_id']);
        }
    }
    /**
     * Get the application type for a given class, worker type, and school.
     *
     * @param int $idclase
     * @param int $tipotrabajador
     * @param int $idcolegio
     * @return string
     */
    public static function getMounthOperations($idclase, $tipotrabajador, $idcolegio)
    {
        return self::where('tuition_id', $idclase)
            ->where('worker_type', $tipotrabajador)
            ->where('school_id', $idcolegio)
            ->value('application');
    }

    // Method to get the minimum tax limit for a tuition
    public static function getMinLimit($tuitionId)
    {
        return self::where('tuition_id', $tuitionId)->value('min_limit');
    }

    // Method to get the maximum tax limit for a tuition
    public static function getMaxLimit($tuitionId)
    {
        return self::where('tuition_id', $tuitionId)->value('max_limit');
    }

    // Method to get the tax value for a tuition
    public static function getTaxValue($tuitionId)
    {
        return self::where('tuition_id', $tuitionId)->pluck('max_value')->first();
    }

    // Method to update or insert operation limits for given tuition IDs
    public static function updOrInsertTopesOperation($tuitionId, $min, $max)
    {
        $operations = self::whereIn('tuition_id', $tuitionId)->get();

        foreach ($operations as $operation) {
            $operation->update([
                'min_limit'  => $min,
                'max_limit'  => $max,
                'updated_at' => now(),
            ]);
        }
    }
    /**
     * Define the relationship with the Tuition model.
     *
     * A Operations belongs to a Tuition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tuition()
    {
        return $this->belongsTo(Tuition::class);
    }
    /**
     * Define the relationship with the School model.
     *
     * A Operation belongs to a School.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
