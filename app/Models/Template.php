<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    // Definir los campos que se pueden asignar de forma masiva
    protected $fillable = [
        'school_id',
        'type',
        'position',
        'code',
        'tuition_id',
        'ignore_zero',
        'parentheses',
    ];

    const TEMPLATE_TYPE_TEACHER = 1;
    const TEMPLATE_TYPE_NON_TEACHER = 2;

    public static function getTemplatesTypes()
    {
        return [
            self::TEMPLATE_TYPE_TEACHER => 'Docente',
            self::TEMPLATE_TYPE_NON_TEACHER => 'No Docente',
        ];
    }

    public static function getLineTypes()
    {

        // Verifica si la configuración existe
        $lineTypes = config('template_types.line_types');
        // Si no existe, devuelve un arreglo vacío o un valor predeterminado
        return $lineTypes ?? [];
    }
    /**
     * Retrieves template data
     */
    public static function getTemplate($schoolId, $type)
    {

        return self::with(['tuition' => function ($query) use ($schoolId) {
            // Relacionamos la tabla Tuition y filtramos por 'school_id'
            $query->where('school_id', $schoolId);
        }])
            ->where('school_id', $schoolId) // Filtramos los templates por el school_id
            ->where('type', $type) // Filtramos los templates por tipo
            ->orderBy('position') // Ordenamos por posición
            ->get(); // Retorna una colección de objetos Template completos

    }

    public static function processTemplates($templates)
    {
        // Iteramos sobre cada plantilla y procesamos según el código
        foreach ($templates as $template) {
            if ($template->code == "_L_") {
                $template->tuition_id = "(Linea de total en segunda columna)";
            } elseif ($template->code == "__L") {
                $template->tuition_id = "(Linea de total en tercera columna)";
            } elseif ($template->code == "_LL") {
                $template->tuition_id = "(Linea de total en segunda y tercera columna)";
            } elseif ($template->code == "LLL") {
                $template->tuition_id = "(Linea de total en todas las columnas)";
            }
        }
        // Devolvemos las plantillas ya procesadas
        return $templates;
    }

/**
 * Checks if a position already exists
 */
    public static function positionExists($schoolId, $type, $position)
    {
        return self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', $position)
            ->exists();
    }

    /**
     * Gets the maximum position that exists
     */
    public static function getMaxPosition($schoolId, $type)
    {
        return self::where('school_id', $schoolId)
            ->where('type', $type)
            ->max('position');
    }

    /**
     * Lists positions of a class in a template
     */
    public static function listTuitionPositionsInTemplate($schoolId, $type, $classId)
    {
        return self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('tuition_id', $classId)
            ->select('position')
            ->get();
    }

    /**
     * Moves a position down from the given position
     */
    public static function movePositionDown($schoolId, $type, $position)
    {
        self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', $position)
            ->increment('position'); // Aumenta la posición en 1
    }

    /**
     * Moves a position up from the given position
     */
    public static function movePositionUp($schoolId, $type, $position)
    {
        self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', $position)
            ->decrement('position'); // Disminuye la posición en 1
    }
    /**
     * Swaps positions
     */
    public static function swapPositions($schoolId, $type, $position1, $position2)
    {
        // Set a temporary position
        self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', $position1)
            ->update(['position' => 0]);

        self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', $position2)
            ->update(['position' => $position1]);

        self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', 0)
            ->update(['position' => $position2]);
    }

    public static function deleteTemplateLine($schoolId, $type, $position)
    {
        return self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', $position)
            ->delete();
    }

    public static function addLine($request)
    {
        $schoolId = $request['school_id'];
        $type = $request['type'];
        $position = $request['position'];
        $code = $request['code'];
        $tuitionId = $request['tuition_id'];
        $ignoreIfZero = $request['ignore_zero'];
        $parentheses = $request['parentheses'];
        $text = $request['text'] ?? ''; // Para código "TEX"

        // Si la posición es mayor que 0, incrementamos la posición
        if ($position > 1) {
            $position = $position + 1;
        }

        // Obtener la posición máxima actual
        $maxPosition = self::getMaxPosition($schoolId, $type);

        // Si la posición máxima es mayor o igual a la posición solicitada, movemos las líneas hacia abajo
        while (self::positionExists($schoolId, $type, $maxPosition) && $maxPosition >= $position) {
            self::movePositionDown($schoolId, $type, $maxPosition);
            $maxPosition = $maxPosition - 1;
        }

        // Si el código es "TEX", asignamos null a tuition_id ya que no hay relación
        if ($code == "TEX" && $tuitionId == "") {
            $tuitionId = $text; // No asociamos un tuition_id
        }

        if (substr($code, 0, 1) != "N") {
            $tuitionId = "";
        }

        // Crear una nueva línea en la plantilla
        return self::create([
            'school_id' => $schoolId,
            'type' => $type,
            'position' => $position,
            'code' => $code,
            'tuition_id' => $tuitionId ?? $text,
            'ignore_zero' => $ignoreIfZero,
            'parentheses' => $parentheses,
        ]);
    }

    public static function updateLine($request)
    {
        $schoolId = $request['school_id'];
        $type = $request['type'];
        $position = $request['position'];
        $code = $request['code'];
        $tuitionId = $request['tuition_id'];
        $ignoreIfZero = $request['ignore_zero'];
        $parentheses = $request['parentheses'];
        $text = $request['text'] ?? ''; // Para código "TEX"

        if ($code == "TEX") {
            $tuitionId = $text;
        }

        // Si el código no comienza con "N", vaciar la clase
        if (substr($code, 0, 1) != "N") {
            $tuitionId = "";
        }

        // Actualizar la línea de la plantilla con los nuevos valores
        return self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', $position)
            ->update([
                'code' => $code,
                'tuition_id' => $tuitionId,
                'ignore_zero' => $ignoreIfZero,
                'parentheses' => $parentheses,
            ]);
    }

    public static function deleteLine($schoolId, $type, $position)
    {
        // Eliminar la línea de la plantilla
        self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', $position)
            ->delete();

        // Ajustar las posiciones de las demás líneas
        $p = $position + 1;
        while (self::positionExists($schoolId, $type, $p)) {
            // Mover la posición de la siguiente línea hacia arriba
            self::movePositionUp($schoolId, $type, $p);
            $p++;
        }
    }

    // Relación con la tabla schools
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function tuition()
    {
        return $this->belongsTo(Tuition::class, 'tuition_id', 'tuition_id');
    }
}
