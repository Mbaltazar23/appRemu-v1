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
        // Load the communes and regions from the configuration
        $communes = config('communes_region.COMMUNE_OPTIONS');
        $regions = config('communes_region.REGIONES_OPTIONS');

        // We define the basic data of the worker
        $workerData = [
            'school_id' => null, // To be assigned later
            'rut' => $this->faker->unique()->numerify('##.###.###-#'),
            'name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'birth_date' => $this->faker->date(),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'commune' => $this->faker->randomElement($communes),
            'region' => $this->faker->randomElement($regions),
            'nationality' => $this->faker->country,
            'marital_status' => $this->faker->randomElement(array_keys(Worker::MARITAL_STATUS)), 
            'worker_type' => $this->faker->randomElement(array_keys(Worker::WORKER_TYPES)), 
            'function_worker' => $this->faker->randomElement(array_keys(Worker::getFunctionWorkerTypes())),
        ];

        return $workerData;
    }
}
