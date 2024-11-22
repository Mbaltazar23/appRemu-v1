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

        // Definimos los datos base del trabajador
        $workerData = [
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

        return $workerData;
    }
}
