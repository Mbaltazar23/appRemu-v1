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

    /**
     * Retrieves template data
     */
    public static function getTemplate($schoolId, $type)
    {
        return self::with(['tuition' => function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        }])
            ->where('school_id', $schoolId)
            ->where('type', $type)
            ->orderBy('position')
            ->get()
            ->map(function ($template) {
                return [
                    'position' => $template->position,
                    'code' => $template->code,
                    'tuition_id' => $template->class_id,
                    'ignore_zero' => $template->ignore_zero,
                    'parentheses' => $template->parentheses,
                ];
            });
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
    // Eliminar la línea de la plantilla
    return self::where('school_id', $schoolId)
        ->where('type', $type)
        ->where('position', $position)
        ->delete();
}

    // Relación con la tabla schools
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function tuition()
    {
        return $this->belongsTo(Tuition::class);
    }
}
