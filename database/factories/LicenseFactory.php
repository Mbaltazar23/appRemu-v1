<?php

namespace Database\Factories;

use App\Models\License;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\License>
 */

class LicenseFactory extends Factory
{
    protected $model = License::class;

    public function definition()
    {
        // Seleccionamos un trabajador aleatorio
        $worker = Worker::inRandomOrder()->first(); // Selecciona un trabajador aleatorio

        $days = rand(1, 10); // Número aleatorio de días de licencia

        // Generamos una fecha de 'issue_date' desde este año hacia adelante
        $currentYear = Carbon::now()->year;
        $issueDate = Carbon::createFromDate($currentYear, rand(1, 12), rand(1, 28)); // Fecha aleatoria de este año

        // Generar las fechas de 'receipt_date' y 'processing_date' dentro de este año
        $receiptDate = Carbon::createFromDate($currentYear, rand(1, 12), rand(1, 28));
        $processingDate = Carbon::createFromDate($currentYear, rand(1, 12), rand(1, 28));

        return [
            'worker_id' => $worker->id,
            'issue_date' => $issueDate->format('Y-m-d'), // Formateamos la fecha
            'reason' => $this->faker->text(30),
            'days' => $days,
            'institution' => $this->faker->company(),
            'receipt_number' => rand(1000, 9999),
            'receipt_date' => $receiptDate->format('Y-m-d'),
            'processing_date' => $processingDate->format('Y-m-d'),
            'responsible_person' => $this->faker->name(),
        ];
    }
}
