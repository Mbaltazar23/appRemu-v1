<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class LicenseSeeder extends Seeder {

    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Crear 30 licencias usando el factory
        $licenses = License::factory()->count(10)->create();
        // Llamar a updateLicenseHours() o updateLicenseDays() según el tipo de trabajador
        foreach ($licenses as $license) {
            // Obtener el trabajador relacionado con la licencia
            $worker = $license->worker;
            // Extraer día, mes y año de la fecha de emisión de la licencia
            $issueDate = \Carbon\Carbon::createFromFormat('Y-m-d', $license->issue_date);
            $day = $issueDate->day;
            $month = $issueDate->month;
            $year = $issueDate->year;
            // Según el tipo de trabajador, llamamos a la función correspondiente
            if ($worker->worker_type == Worker::WORKER_TYPE_TEACHER) {
                // Si es docente, asignamos las horas
                $license->updateLicenseHours($day, $month, $year, $license->days);
            }
            if ($worker->worker_type == Worker::WORKER_TYPE_NON_TEACHER) {
                // Si no es docente, asignamos los días
                $license->updateLicenseDays($day, $month, $year, $license->days);
            }
        }
    }

}
