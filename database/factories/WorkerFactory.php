<?php

namespace Database\Factories;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Worker>
 */
class WorkerFactory extends Factory
{
    protected $model = Worker::class;

    public function definition()
    {

        // Cargar las comunas y regiones desde la configuración
        $communes = config('communes_region.COMMUNE_OPTIONS');
        $regions = config('communes_region.REGIONES_OPTIONS');
        return [
            'school_id' => null, // Se asignará más tarde
            'rut' => $this->faker->unique()->numerify('########-#'),
            'name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'birth_date' => $this->faker->date(),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'commune' => $this->faker->randomElement($communes),
            'region' => $this->faker->randomElement($regions),
            'nationality' => $this->faker->country,
            'marital_status' => $this->faker->randomElement([1, 2, 3, 4]), // Estado civil
            'worker_type' => $this->faker->randomElement([1, 2]), // Docente o No docente
            'function_worker' => $this->faker->randomElement(array_keys(Worker::getFunctionWorkerTypes())),
        ];

        // Si worker_type es 1 (Docente), asignamos horas aleatorias
        if ($workerData['worker_type'] === 1) {
            $workerData['load_hourly_work'] = json_encode([
                'lunes' => rand(0, 8),
                'martes' => rand(0, 8),
                'miercoles' => rand(0, 8),
                'jueves' => rand(0, 8),
                'viernes' => rand(0, 8),
                'sabado' => rand(0, 8),
            ]);
        } else {
            // Si worker_type es 2 (No docente), asignamos 0 a los días lunes a viernes, y sabado también a 0
            $workerData['load_hourly_work'] = json_encode([
                'lunes' => 0,
                'martes' => 0,
                'miercoles' => 0,
                'jueves' => 0,
                'viernes' => 0,
                'sabado' => 0, // Puede mantenerse a 0, aunque no es necesario
            ]);
        }

        return $workerData;

    }
}
