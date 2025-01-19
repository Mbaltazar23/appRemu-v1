<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tuition extends Model // Class
{
    use HasFactory;
    // Attributes that can be mass assigned
    protected $fillable = [
        'title',
        'tuition_id', // Includes the new field
        'type',
        'description',
        'in_liquidation',
        'editable',
        'school_id',
    ];
    /**
     * Check if a tuition exists for a specific school.
     */
    public static function exists($tuitionId, $school_id)
    {
        return self::where('tuition_id', $tuitionId)
            ->where('school_id', $school_id)
            ->exists();
    }
    /**
     * Create a new Tuition record.
     */
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
    /**
     * Generate a unique tuition name based on the provided type.
     */
    public static function createUniqueTuition($title, $type, $school_id, $uniqueType = 'time')
    {
        // Generate a unique name based on the type
        $i = 0;
        do {
            if ($uniqueType === 'valor') {
                $nombre = substr(md5($title . $type . "valor" . $i), 0, 40);
            } else {
                $nombre = substr(md5($title . $type . $i . time()), 0, 40);
            }
            $i++;
        } while (Tuition::exists($nombre, $school_id));

        return $nombre; // Returns the generated name
    }
    /**
     * Update the title of a Tuition record.
     */
    public static function updateTitleTuition($classId, $title, $schoolId)
    {
        self::where('tuition_id', $classId)
            ->where('school_id', $schoolId)
            ->update(['title' => $title]);
    }
    /**
     * Delete a Tuition record based on tuition_id and school_id.
     */
    public static function deleteTuition($tuition_id, $school_id)
    {
        // Find the tuition that matches tuition_id and school_id
        self::where('tuition_id', $tuition_id)
            ->where('school_id', $school_id)
            ->delete();
    }
    /**
     * Retrieve the title of a Tuition based on tuition_id and school_id.
     */
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
    /**
     * Retrieve all liquidation titles by school ID.
     */
    public static function getLiquidationTitlesBySchool($schoolId)
    {
        // Get distinct titles and tuition_id of classes in liquidation for a school
        return self::where('in_liquidation', 1) // Filter by classes in liquidation
            ->where('school_id', $schoolId) // Filter by school ID
            ->orderBy('title') // Order alphabetically by 'title'
            ->distinct() // Get only distinct titles
            ->get(['title', 'tuition_id']); // Get 'title' and 'tuition_id' fields
    }
    /**
     * Get the tuition and related operation details for a specific tuition ID and worker type.
     */
    public static function getTuitionAndOperationDetails($tuitionId, $workerTypeId, $schoolId)
    {
        // Retrieve tuition details
        $tuitionDetails = self::select('type', 'in_liquidation')
            ->where('tuition_id', $tuitionId)
            ->where('school_id', $schoolId)
            ->first(); // Returns the first matching record
        // Initialize default values
        $operationType = $tuitionDetails->type;
        $operation = "";
        $unitLimit = "";
        $minLimit = 0;
        $maxLimit = 0;
        $maxValueLimit = 0;
        $months = "";
        $workerType = "";
        $inLiquidation = $tuitionDetails->in_liquidation ?? 0; // Assign in_liquidation from tuition
        // Retrieve related operation details based on tuition ID and worker type
        $operationDetailsFromDb = Operation::select('operation', 'limit_unit', 'min_limit', 'max_limit', 'max_value', 'application', 'worker_type')
            ->where('tuition_id', $tuitionId)
            ->where('school_id', $schoolId)
            ->where('worker_type', $workerTypeId)
            ->first(); // Returns the first matching operation
        // If operation details are found, assign them to the variables
        if ($operationDetailsFromDb) {
            $operation = $operationDetailsFromDb->operation;
            $unitLimit = $operationDetailsFromDb->limit_unit;
            $minLimit = $operationDetailsFromDb->min_limit;
            $maxLimit = $operationDetailsFromDb->max_limit;
            $maxValueLimit = $operationDetailsFromDb->max_value;
            $months = $operationDetailsFromDb->application;
            $workerType = $operationDetailsFromDb->worker_type;
        }
        // Return the values as an object
        return (object) [
            'type' => $operationType,
            'operation' => $operation,
            'limit_unit' => $unitLimit,
            'min_limit' => $minLimit,
            'max_limit' => $maxLimit,
            'max_value' => $maxValueLimit,
            'application' => $months,
            'worker_type' => $workerType,
            'in_liquidation' => $inLiquidation,
        ];
    }
    /**
     * Relationship: A Tuition belongs to a School.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relationship with the Parameter model (tuition_id in Parameter).
     */
    public function parameters()
    {
        return $this->hasMany(Parameter::class);
    }

    /**
     * Relationship with the Operation model (tuition_id in Operation).
     */
    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    /**
     * Relationship with Template.
     */
    public function templates()
    {
        return $this->hasMany(Template::class);
    }
}
