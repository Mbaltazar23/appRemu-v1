<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model {

    use HasFactory;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'worker_id',
        'issue_date',
        'reason',
        'days',
        'institution',
        'receipt_number',
        'receipt_date',
        'processing_date',
        'responsible_person',
    ];

    /**
     * Get all licenses by school ID.
     *
     * This method fetches licenses for a particular school by checking the associated worker's school ID.
     *
     * @param int $school_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getLicensesBySchool($school_id) {
        return self::whereHas('worker', function ($query) use ($school_id) {
                    $query->where('school_id', $school_id);
                })->orderBy('id', 'ASC');
    }

    /**
     * Update the license hours for a given day, month, and year.
     *
     * This method updates the hours of a license by breaking down the days and assigning hours for each
     * according to the worker’s schedule.
     *
     * @param int $day
     * @param int $month
     * @param int $year
     * @param int $days
     */
    public function updateLicenseHours($day, $month, $year, $days) {
        // First, delete the existing hours for the license
        $this->deleteLicenseHours();
        // Get the worker associated with the license
        $worker = $this->worker;
        $loadHourlyWork = json_decode($worker->load_hourly_work, true);
        // Define hours for each weekday (Monday to Saturday)
        $d1 = $loadHourlyWork['lunes'] ?? 0;
        $d2 = $loadHourlyWork['martes'] ?? 0;
        $d3 = $loadHourlyWork['miercoles'] ?? 0;
        $d4 = $loadHourlyWork['jueves'] ?? 0;
        $d5 = $loadHourlyWork['viernes'] ?? 0;
        $d6 = $loadHourlyWork['sabado'] ?? 0;
        // Process and update the hours for the given days
        do {
            $remainingHours = 0;
            $initialDate = mktime(0, 0, 0, $month, $day, $year); // Generate timestamp
            $day = date("d", $initialDate);
            $month = date("m", $initialDate);
            $year = date("y", $initialDate);
            // Get the weekday
            $weekday = date("w", $initialDate);
            // Assign hours based on the weekday
            if ($weekday == 1) {
                $remainingHours = $d1;
            }
            if ($weekday == 2) {
                $remainingHours = $d2;
            }
            if ($weekday == 3) {
                $remainingHours = $d3;
            }
            if ($weekday == 4) {
                $remainingHours = $d4;
            }
            if ($weekday == 5) {
                $remainingHours = $d5;
            }
            if ($weekday == 6) {
                $remainingHours = $d6;
            }
            // We subtract the days according to how many they are
            $days -= 1;
            // Insert the license hours for the specific day
            $this->hours()->create([
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'hours' => $remainingHours,
            ]);
            // Increment the day
            $day++;
        } while ($days > 0);
    }

    /**
     * Update the license days for a given day, month, and year.
     *
     * This method updates the days of the license by breaking down the days and marking them as available.
     *
     * @param int $day
     * @param int $month
     * @param int $year
     * @param int $days
     */
    public function updateLicenseDays($day, $month, $year, $days) {
        // Delete previously registered license days
        $this->deleteLicenseDays();
        // Process and update the days for the given period
        do {
            $available = 0;
            $initialDate = mktime(0, 0, 0, $month, $day, $year); // Generate timestamp
            $day = date("d", $initialDate);
            $month = date("m", $initialDate);
            $year = date("y", $initialDate);
            // Get the weekday
            $weekday = date("w", $initialDate);
            // Mark the day as available if it's a workday
            if ($weekday >= 0 && $weekday <= 6) {
                $available = 1;
            }
            // We subtract the days according to how many they are
            $days -= 1;
            // Insert the license day for the specific day
            $this->days()->create([
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'exists' => $available,
            ]);
            // Increment the day
            $day++;
        } while ($days > 0);
    }

    /**
     * Sum the total hours of a specific worker for a given period.
     *
     * This static method sums the total license hours for a worker in a specific month and year
     * between a given start and end day.
     *
     * @param int $workerId
     * @param int $month
     * @param int $year
     * @param int $startDay
     * @param int $endDay
     * @return int
     */
    public static function sumLicenseHours($workerId, $month, $year, $startDay, $endDay) {
        $licenseHours = LicenseHour::whereHas('license', function ($query) use ($workerId, $month, $year, $startDay, $endDay) {
                    $query->where('worker_id', $workerId)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->where('day', '>', $startDay)
                    ->where('day', '<', $endDay);
                })
                ->get(['hours']);

        // Return the total count of hours
        return $licenseHours->count();
    }

    /**
     * Sum the total days of a specific worker’s license.
     *
     * This static method sums the total license days for a worker in a given month and year
     * between a given start and end day.
     *
     * @param int $worker_id
     * @param int $mes
     * @param int $year
     * @param int $fromDay
     * @param int $until
     * @return int
     */
    public static function sumDaysLicence($worker_id, $mes, $year, $fromDay, $until) {
        $totalDays = LicenseDay::whereHas('license', function ($query) use ($worker_id, $mes, $year, $fromDay, $until) {
                    $query->where('worker_id', $worker_id)
                    ->where('month', $mes)
                    ->where('year', $year)
                    ->where('day', '>', $fromDay)
                    ->where('day', '<', $until);
                })
                ->sum('exists');
        // Return the total sum or 0 if no records are found
        return $totalDays;
    }

    /**
     * Delete all license hours.
     *
     * This method deletes all the hours associated with the license.
     */
    public function deleteLicenseHours() {
        $this->hours()->delete();
    }

    /**
     * Delete all license days.
     *
     * This method deletes all the days associated with the license.
     */
    public function deleteLicenseDays() {
        $this->days()->delete();
    }

    /**
     * Define the relationship to the Worker model.
     *
     * This defines the relationship between a license and a worker.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function worker() {
        return $this->belongsTo(Worker::class);
    }

    /**
     * Relationship to LicenseDay.
     *
     * This defines the relationship to the LicenseDay model, where each license can have multiple days.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function days() {
        return $this->hasMany(LicenseDay::class, 'license_id');
    }

    /**
     * Relationship to LicenseHour.
     *
     * This defines the relationship to the LicenseHour model, where each license can have multiple hours.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hours() {
        return $this->hasMany(LicenseHour::class, 'license_id');
    }

}
