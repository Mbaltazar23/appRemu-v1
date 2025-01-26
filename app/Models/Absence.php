<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model {

    use HasFactory;

    // Attributes that can be mass-assigned
    protected $fillable = [
        'worker_id', // Relationship with the worker
        'day', // Day of the absence
        'month', // Month of the absence
        'year', // Year of the absence
        'reason', // Reason for the absence
        'minutes', // Duration in minutes
        'with_consent', // If the absence has consent
    ];

    /**
     * Get the full date as a single attribute.
     * 
     * This method combines the day, month, and year attributes into a single date string (YYYY-MM-DD).
     * If the day, month, or year is not provided, it returns null.
     *
     * @return string|null The full date in YYYY-MM-DD format, or null if any of the date components are missing.
     */
    public function getDateAttribute() {
        // Ensure the values are present and valid
        if ($this->day && $this->month && $this->year) {
            return Carbon::create($this->year, $this->month, $this->day)->toDateString();
        }
        return null;
    }

    /**
     * Set the date from a single 'date' field.
     * 
     * This method splits the provided 'date' into day, month, and year, and sets the corresponding model attributes.
     * It uses the Carbon library to parse the input date.
     *
     * @param string $value The date to be set in the format 'YYYY-MM-DD'.
     */
    public function setDateAttribute($value) {
        // If the 'date' value is valid, split the date
        if ($value) {
            $date = Carbon::parse($value); // Converts the date to a Carbon object
            $this->attributes['day'] = $date->day;
            $this->attributes['month'] = $date->month;
            $this->attributes['year'] = $date->year;
        }
    }

    /**
     * Method to sum the minutes of absence.
     * 
     * This method calculates the total minutes of absence within a given date range.
     * It filters the absences by worker ID, month, year, and date range, and then sums the 'minutes' for those absences that have consent.
     *
     * @param int $workerId The ID of the worker whose absences are being calculated.
     * @param int $month The month for which absences are calculated.
     * @param int $year The year for which absences are calculated.
     * @param int $fromDay The starting day of the range.
     * @param int $toDate The ending day of the range.
     * 
     * @return int The total minutes of absence.
     */
    public static function sumAbsenceMinutes($workerId, $month, $year, $fromDay, $toDate) {
        // Make sure to convert the dates into Carbon objects if they're not already
        return self::where('worker_id', $workerId)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->where('day', '>', $fromDay) // Compares with the day of fromDate
                        ->where('day', '<', $toDate) // Compares with the day of toDate
                        ->where('with_consent', 1) // Filters only the absences with consent
                        ->sum('minutes'); // Sums the minutes for the absences
    }

    /**
     * Relationship with Worker (An absence belongs to a worker).
     * 
     * This method defines the relationship between the Absence and Worker models. It indicates that each absence record belongs to one worker.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship instance.
     */
    public function worker() {
        return $this->belongsTo(Worker::class);
    }

}
