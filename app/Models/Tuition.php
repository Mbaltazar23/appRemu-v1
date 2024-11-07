<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public static function addTuition($name, $title, $type, $editable, $schoolId)
    {
        return self::create([
            'tuition_id' => $name,
            'title' => $title,
            'type' => $type,
            'description' => $title,
            'in_liquidation' => 1,
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
        if ($tuitionId == "") return "";

        $result = self::where('tuition_id', $tuitionId)
            ->where('school_id', $schoolId)
            ->first(['title']);

        return $result ? $result->title : "";
    }

    // Relación: Una Tuition pertenece a una School
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Relación: Una Tuition tiene muchas Operations
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
