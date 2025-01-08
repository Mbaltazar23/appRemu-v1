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
            $query->where('school_id', $school_id);
        })->orderBy('id', 'ASC');
    }

    public function updateLicenseHours($day, $month, $year, $days)
    {
        // Primero, borramos las horas de la licencia
        $this->deleteLicenseHours();
        // Obtener el trabajador asociado a la licencia
        $worker = $this->worker;
        $loadHourlyWork = json_decode($worker->load_hourly_work, true);
        // Definimos las horas para cada día de la semana (lunes a sábado)
        $d1 = $loadHourlyWork['lunes'] ?? 0;
        $d2 = $loadHourlyWork['martes'] ?? 0;
        $d3 = $loadHourlyWork['miercoles'] ?? 0;
        $d4 = $loadHourlyWork['jueves'] ?? 0;
        $d5 = $loadHourlyWork['viernes'] ?? 0;
        $d6 = $loadHourlyWork['sabado'] ?? 0;
        // Proceso de actualización de horas
        do {
            $remainingHours = 0;
            $initialDate = mktime(0, 0, 0, $month, $day, $year); // Generamos el timestamp
            $day = date("d", $initialDate);
            $month = date("m", $initialDate);
            $year = date("y", $initialDate);
            // Obtenemos el día de la semana
            $weekday = date("w", $initialDate);
            // Asignamos las horas según el día de la semana
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
            $days -= 1;
            // Insertamos las horas de licencia usando la relación
            $this->hours()->create([
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'hours' => $remainingHours,
            ]);
            // Incrementamos el día
            $day++;
        } while ($days > 0);
    }

    public function updateLicenseDays($day, $month, $year, $days)
    {
        // Borrar los días de la licencia previamente registrada
        $this->deleteLicenseDays();
        // Proceso de actualización de días
        do {
            $available = 0;
            $initialDate = mktime(0, 0, 0, $month, $day, $year); // Generamos el timestamp
            $day = date("d", $initialDate);
            $month = date("m", $initialDate);
            $year = date("y", $initialDate);
            // Obtenemos el día de la semana
            $weekday = date("w", $initialDate);
            // Verificamos si es un día laborable
            if ($weekday == 0 || $weekday == 1 || $weekday == 2 || $weekday == 3 || $weekday == 4 || $weekday == 5 || $weekday == 6) {
                $available = 1;
            }
            $days -= 1;
            // Insertamos el día de licencia usando la relación
            $this->days()->create([
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'exists' => $available,
            ]);
            // Incrementamos el día
            $day++;
        } while ($days > 0);
    }

    // Método para contar las horas de licencia
    public static function sumLicenseHours($workerId, $month, $year, $startDay, $endDay)
    {
        // Obtener todas las horas de licencia dentro de las condiciones
        $licenseHours = LicenseHour::whereHas('license', function ($query) use ($workerId, $month, $year, $startDay, $endDay) {
            $query->where('worker_id', $workerId)
                ->where('month', $month)
                ->where('year', $year)
                ->where('day', '>', $startDay)
                ->where('day', '<', $endDay);
        })
            ->get(['hours']); // Selecciona solo el campo 'hours'

        return $licenseHours->count(); // Retorna una colección de objetos LicenseHour
    }

    //Metodo para sumar los dias de licencias
    public static function sumDaysLicence($worker_id, $mes, $year, $fromDay, $hasta)
    {
        $totalDays = LicenseDay::whereHas('license', function ($query) use ($worker_id, $mes, $year, $fromDay, $hasta) {
            $query->where('worker_id', $worker_id)
                ->where('month', $mes)
                ->where('year', $year)
                ->where('day', '>', $fromDay)
                ->where('day', '<', $hasta);
        })
            ->sum('exists'); //

        // Return the count or 0 if no records are found
        return $totalDays;
    }

    public function deleteLicenseHours()
    {
        // Eliminamos las horas de la licencia usando Eloquent
        $this->hours()->delete();
    }

    public function deleteLicenseDays()
    {
        // Eliminamos los días de la licencia usando Eloquent
        $this->days()->delete();
    }

    /**
     * Relación uno a muchos con worker.
     */
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    // Relación con los días de licencia
    public function days()
    {
        return $this->hasMany(LicenseDay::class, 'license_id');
    }

    // Relación con las horas de licencia
    public function hours()
    {
        return $this->hasMany(LicenseHour::class, 'license_id');
    }
}
