<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
    // Define the fields that can be mass-assigned
    protected $fillable = [
        'school_id',
        'type',
        'position',
        'code',
        'tuition_id',
        'ignore_zero',
        'parentheses',
    ];
    // the types of staff being 2 (1 = Docente, 2 = No Docente)
    const TEMPLATE_TYPE_TEACHER = 1;
    const TEMPLATE_TYPE_NON_TEACHER = 2;
    // We define an arrangement where the types of forms to be inserted will be grouped
    public static function getTemplatesTypes()
    {
        return [
            self::TEMPLATE_TYPE_TEACHER => 'Docente',
            self::TEMPLATE_TYPE_NON_TEACHER => 'No Docente',
        ];
    }
    // We obtain the line types to insert into the model
    public static function getLineTypes()
    {

        // Check if the configuration exists
        $lineTypes = config('template_types.line_types');
        // If it doesn't exist, return an empty array or a default value
        return $lineTypes ?? [];
    }
    /**
     * Retrieves template data
     */
    public static function getTemplate($schoolId, $type)
    {

        return self::with(['tuition' => function ($query) use ($schoolId) {
            // Relates the Tuition table and filters by 'school_id'
            $query->where('school_id', $schoolId);
        }])
            ->where('school_id', $schoolId) // Filters templates by school_id
            ->where('type', $type) // Filters templates by type
            ->orderBy('position') // Orders by position
            ->get(); // Returns a collection of complete Template objects

    }
    // We process the form to evaluate whether or not the code will put a space between each one.
    public static function processTemplates($templates)
    {
        // Iterates over each template and processes it based on the code
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
        // Returns the processed templates
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
            ->increment('position'); // Increases the position by 1
    }
    /**
     * Moves a position up from the given position
     */
    public static function movePositionUp($schoolId, $type, $position)
    {
        self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', $position)
            ->decrement('position'); // Decreases the position by 1
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
    // Delete the line or position of the template item
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
        $text = $request['text'] ?? ''; // For code "TEX"
        // If the position is greater than 0, we increment the position
        if ($position > 1) {
            $position = $position + 1;
        }
        // Get the current maximum position
        $maxPosition = self::getMaxPosition($schoolId, $type);
        // If the maximum position is greater than or equal to the requested position, move lines down
        while (self::positionExists($schoolId, $type, $maxPosition) && $maxPosition >= $position) {
            self::movePositionDown($schoolId, $type, $maxPosition);
            $maxPosition = $maxPosition - 1;
        }
        // If the code is "TEX", assign null to tuition_id since there is no relationship
        if ($code == "TEX" && $tuitionId == "") {
            $tuitionId = $text; // Do not associate a tuition_id
        }
        // We evaluate if the code is different from N
        if (substr($code, 0, 1) != "N") {
            $tuitionId = "";
        }
        // Create a new line in the template
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
        $text = $request['text'] ?? ''; // For code "TEX"
        // We evaluate the code that if it is TEXT, the class will be the value of the text field
        if ($code == "TEX") {
            $tuitionId = $text;
        }
        // If the code does not start with "N", clear the class
        if (substr($code, 0, 1) != "N") {
            $tuitionId = "";
        }
        // Update the line in the template with the new values
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
    // Delete Line from the Template form
    public static function deleteLine($schoolId, $type, $position)
    {
        // Delete the line from the template
        self::where('school_id', $schoolId)
            ->where('type', $type)
            ->where('position', $position)
            ->delete();

        // Adjust the positions of the remaining lines
        $p = $position + 1;
        while (self::positionExists($schoolId, $type, $p)) {
            // Move the position of the next line up
            self::movePositionUp($schoolId, $type, $p);
            $p++;
        }
    }
    /**
     * Get the school that owns the template
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the tuition associated with the template
     */
    public function tuition()
    {
        return $this->belongsTo(Tuition::class, 'tuition_id', 'tuition_id');
    }
}
