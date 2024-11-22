<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

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

    public static function getLicensesBySchool($school_id)
    {
        return self::whereHas('worker', function ($query) use ($school_id) {
            // Pasamos el $school_id al closure
            $query->where('school_id', $school_id);
        })
            ->orderBy('id', 'ASC');
    }
    /**
     * Elimina la licencia junto con sus horas y días asociados.
     */
    public function deleteWithHoursAndDays()
    {
        $this->hourLicenses()->delete(); // Usar la relación correctamente
        $this->delete(); // Eliminar la licencia
    }

    public function updateLicenseHours($startDay, $month, $year, $days)
    {
        $worker = $this->worker; // Obtener el trabajador asociado a esta licencia
        $loadHourlyWork = json_decode($worker->load_hourly_work, true);

        // Asignar horas para lunes a sábado, no se usa domingo
        $d1 = $loadHourlyWork['lunes'] ?? 0;
        $d2 = $loadHourlyWork['martes'] ?? 0;
        $d3 = $loadHourlyWork['miercoles'] ?? 0;
        $d4 = $loadHourlyWork['jueves'] ?? 0;
        $d5 = $loadHourlyWork['viernes'] ?? 0;
        $d6 = $loadHourlyWork['sabado'] ?? 0;

        $currentDay = $startDay;
        $hoursArray = [$d1, $d2, $d3, $d4, $d5, $d6]; // Solo 6 elementos: lunes a sábado
        $daysRemaining = $days;

        // Insertar las horas de licencia
        while ($daysRemaining > 0) {
            // Obtener el día de la semana
            $timestamp = mktime(0, 0, 0, $month, $currentDay, $year);
            $dayOfWeek = date('w', $timestamp); // 0 = domingo, 6 = sábado

            // Verificamos que el día no sea domingo (0), porque no tenemos horas asignadas para domingo
            if ($dayOfWeek != 0) { // Si no es domingo
                // Ajustamos el índice para lunes (1) a sábado (6)
                $hoursToAssign = $hoursArray[$dayOfWeek - 1]; // Se ajusta el índice

                // Crear o actualizar las horas de licencia para ese día
                HourLicense::updateOrCreate(
                    ['license_id' => $this->id, 'day' => $currentDay, 'month' => $month, 'year' => $year],
                    ['hours' => $hoursToAssign]
                );
            }

            $currentDay++;
            $daysRemaining--; // Decrementar los días restantes
        }
    }

    public function updateLicenseDays($startDay, $month, $year, $days)
    {
        $currentDay = $startDay;
        $daysRemaining = $days;

        // Insertar los días de licencia (sin horas asignadas)
        while ($daysRemaining > 0) {
            // Insertar día de licencia (sin horas asignadas)
            HourLicense::insertOrUpdateDays(
                $this->id, // Aquí pasamos el license_id
                $currentDay,
                $month,
                $year,
                1// 'exists' se puede poner como 1 (o según lo que necesites)
            );
            $currentDay++;
            $daysRemaining--;
        }
    }

    // Método para contar las horas de licencia
    public static function sumLicenseHours($workerId, $month, $year, $startDay, $endDay)
    {
        // Sum the 'exists' field (which represents 'Hay' in your original table)
        $licenseHours = HourLicense::selectRaw('hour_licenses.hours')
            ->leftJoin('licenses', 'hour_licenses.license_id', '=', 'licenses.id') // Left join with licenses table
            ->where('licenses.worker_id', $workerId) // Filter by worker ID
            ->where('hour_licenses.month', $month) // Filter by month
            ->where('hour_licenses.year', $year) // Filter by year
            ->where('hour_licenses.day', '>', $startDay) // Filter by day range (greater than start day)
            ->where('hour_licenses.day', '<', $endDay) // Filter by day range (less than end day)
            ->first(); // Get the first row, as we are selecting a single sum

        // Return the sum or 0 if no records are found
        return $licenseHours ;
    }

    //Metodo para sumar los dias de licencias

    public static function sumDaysLicence($idTrabajador, $mes, $year, $fromDay, $hasta)
    {
        return HourLicense::selectRaw('COUNT(DISTINCT hour_licenses.day) as total_days')
            ->leftJoin('licenses', 'hour_licenses.license_id', '=', 'licenses.id') // Left join with licenses table
            ->where('licenses.worker_id', $idTrabajador) // Filter by worker ID
            ->where('hour_licenses.month', $mes) // Filter by month
            ->where('hour_licenses.year', $year) // Filter by year
            ->where('hour_licenses.day', '>', $fromDay) // Filter by day range (greater than from day)
            ->where('hour_licenses.day', '<', $hasta) // Filter by day range (less than until day)
            ->first(); // Get the first row

        // Return the count or 0 if no records are found
        return $licenseDays ? $licenseDays->total_days : 0;
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    // Cambiar aquí: hacer que hourLicenses() no sea estático
    public function hourLicenses()
    {
        return $this->hasMany(HourLicense::class); // No es necesario ser estático
    }
}
