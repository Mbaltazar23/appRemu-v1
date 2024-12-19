<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tuition extends Model//Clase

{
    use HasFactory;

    // Atributos que pueden ser asignados masivamente
    protected $fillable = [
        'title',
        'tuition_id', // Incluye el nuevo campo
        'type',
        'description',
        'in_liquidation',
        'editable',
        'school_id',
    ];

    // Modificar el método exists para usar el nuevo campo
    public static function exists($tuitionId, $school_id)
    {
        return self::where('tuition_id', $tuitionId)
            ->where('school_id', $school_id)
            ->exists();
    }

    // Crear un nuevo método para añadir Tuition usando el nuevo campo
    public static function addTuition($name, $title, $type, $liquidation, $editable, $schoolId)
    {
        return self::create([
            'tuition_id' => $name,
            'title' => $title,
            'type' => $type,
            'in_liquidation' => $liquidation,
            'editable' => $editable,
            'school_id' => $schoolId,
        ]);
    }

    public static function createUniqueTuition($title, $type, $school_id, $uniqueType = 'time')
    {
        // Generar el nombre único basado en el tipo
        $i = 0;
        do {
            if ($uniqueType === 'valor') {
                $nombre = substr(md5($title . $type . "valor" . $i), 0, 40);
            } else {
                $nombre = substr(md5($title . $type . $i . time()), 0, 40);
            }
            $i++;
        } while (Tuition::exists($nombre, $school_id));

        return $nombre; // Devuelve el nombre generado
    }

    public static function updateTitleTuition($classId, $title, $schoolId)
    {
        self::where('tuition_id', $classId)
            ->where('school_id', $schoolId)
            ->update(['title' => $title]);
    }

    public static function deleteTuition($tuition_id, $school_id)
    {
        // Busca la matrícula que coincida con tuition_id y school_id
        self::where('tuition_id', $tuition_id)
            ->where('school_id', $school_id)
            ->delete();
    }

    public static function getTuitionTitle($tuitionId, $schoolId)
    {
        if ($tuitionId == "") {
            return "";
        }

        $result = self::where('tuition_id', $tuitionId)
            ->where('school_id', $schoolId)
            ->first();

        return $result ? $result->title : "";
    }

    public static function getLiquidationTitlesBySchool($schoolId)
    {
        // Obtener los títulos distintos y tuition_id de las clases en liquidación de un colegio
        return self::where('in_liquidation', 1) // Filtrar las clases en liquidación
            ->where('school_id', $schoolId) // Filtrar por el ID del colegio
            ->orderBy('title') // Ordenar alfabéticamente por 'title'
            ->distinct() // Obtener solo títulos distintos
            ->get(['title', 'tuition_id']); // Obtener los campos 'title' y 'tuition_id'
    }
    // Método para obtener las operaciones por clase (tuition), tipo de trabajador y colegio
    public static function getOperationsByTuitionAndWorkerType($tuitionId, $workerTypeId, $schoolId)
    {
        // First, attempt to get the operation data
        $result = self::select(
            'tuitions.title',
            'tuitions.tuition_id',
            'tuitions.type',
            'operations.operation',
            'operations.limit_unit',
            'operations.min_limit',
            'operations.max_limit',
            'operations.max_value',
            'operations.application',
            'tuitions.in_liquidation',
            'operations.worker_type'
        )
            ->leftJoin('operations', 'operations.tuition_id', '=', 'tuitions.tuition_id')
            ->where('tuitions.school_id', $schoolId)
            ->where('operations.worker_type', $workerTypeId)
            ->where('tuitions.title', $tuitionId)
            ->first();

        return $result;
    }

    public static function getOperationsWithTuitionAndTemplates($tuitionId, $workerType, $schoolId)
    {
        $result = self::select(
            'tuitions.type',
            'op.operation',
            'op.limit_unit',
            'op.min_limit',
            'op.max_limit',
            'op.max_value',
            'op.application',
            'tuitions.in_liquidation',
            'op.worker_type'
        )
            ->leftJoin('operations as op', 'op.tuition_id', '=', 'tuitions.tuition_id') // Alias para 'operations'
            ->leftJoin('parameters', 'parameters.name', '=', 'tuitions.tuition_id')
            ->whereRaw('(tuitions.tuition_id = op.tuition_id or parameters.name = tuitions.tuition_id)')
            ->where('tuitions.school_id', $schoolId)
            ->where('tuitions.tuition_id', $tuitionId)
            ->where('op.school_id', $schoolId) // Usamos el alias 'op' para referirnos a 'operations'
            ->where('op.worker_type',$workerType) // Usamos el alias 'op' para referirnos a 'operations'
            ->first();

        return $result;
    }

    // Método para obtener el título del parámetro
    public static function getTitleOfParameter($tuitionId, $workerId, $workerTypeId, $schoolId)
    {
        // Obtener la descripción de la clase
        $classDescription = self::getTuitionTitleAndDescription($tuitionId, $schoolId);
        // Si la clase tiene una descripción, devolverla
        if (!empty($classDescription)) {
            // Devolver la descripción del parámetro asociado
            return Parameter::where('tuition_id', $classDescription['description'])
                ->where('worker_id', $workerId)
                ->where('worker_type', $workerTypeId)
                ->where('school_id', $schoolId)
                ->first()->description ?? '';
        }

        // Si no tiene descripción, devolver el título de la clase
        return self::getTuitionTitle($tuitionId, $schoolId);
    }

    /**
     * Método para obtener las matrículas relacionadas con liquidaciones
     * donde el tuition_id en details coincide con el tuition_id en tuitions.
     */
    public static function getTuitionWithLiquidations()
    {
        return self::leftJoin('liquidations', function ($join) {
            // Usamos JSON_EXTRACT para extraer el tuition_id desde el campo 'details'
            $join->on('tuitions.tuition_id', '=', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(liquidations.details, '$.tuition_id'))"));
        })
            ->where('tuitions.in_liquidation', 1)
            ->where('tuitions.editable', 1)
            ->where('tuitions.description', '!=', '')
            ->select('tuitions.*', 'liquidations.*') // O ajusta los campos que deseas seleccionar
            ->get();
    }

    // Relación: Una Tuition pertenece a una School
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Relación con el modelo Parameter (tuition_id en Parameter)
    public function parameters()
    {
        return $this->hasMany(Parameter::class);
    }

    // Relación con el modelo Operation (tuition_id en Operation)
    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    //Relacion con Template
    public function templates()
    {
        return $this->hasMany(Template::class);
    }
}
