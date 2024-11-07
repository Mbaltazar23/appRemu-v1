<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Parameter;
use App\Models\School;
use App\Models\User;
use App\Models\Worker;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear una instancia de Faker
        $faker = Faker::create();

        // Asociar solo usuarios "Contador" con colegios
        $contadorUsers = User::where('role', User::CONTADOR)->get();

        foreach ($contadorUsers as $user) {
            // Obtener un colegio aleatorio
            $schoolId = School::inRandomOrder()->first()->id;

            // Crear varios trabajadores
            $workers = Worker::factory()->count(6)->create(['school_id' => $schoolId]);

            foreach ($workers as $worker) {
                // Crear un contrato para cada trabajador
                $contract = Contract::create([
                    'worker_id' => $worker->id,
                    'contract_type' => rand(1, 4),
                    'hire_date' => now()->toDateString(),
                    'termination_date' => now()->addYear()->toDateString(),
                    'replacement_reason' => null,
                ]);

                // Decidir aleatoriamente si crear detalles del contrato
                if (rand(0, 1) === 1) { // Si el valor es 1, crear detalles
                    // Generación de los detalles del contrato
                    $details = [
                        'city' => $faker->city,
                        'duration' => $faker->randomElement(['Indefinido', 'Plazo fijo', 'Reemplazo']),
                        'total_remuneration' => rand(400000, 1200000),
                        'remuneration_gloss' => $faker->word,
                        'origin_city' => $faker->city,
                        'schedule' => $faker->randomElement(['Mañana', 'Tarde', 'Nocturna']),
                    ];

                    // Solo si el worker_type es 1 (Docente), agregamos 'levels' al detalle
                    if ($worker->worker_type === Worker::WORKER_TYPE_TEACHER) {
                        $details['levels'] = $faker->randomElement(['Básica', 'Media', 'Superior']);
                    }

                    // Actualizamos los detalles del contrato
                    $contract->update(['details' => json_encode($details)]);
                } else {
                    // Si no se crean los detalles, se pueden dejar vacíos o no asignar nada
                    $contract->update(['details' => null]); // O simplemente no asignar el campo
                }

                // Simular datos de la solicitud
                $requestData = [
                    'worker_type' => Worker::WORKER_TYPE_TEACHER,
                    'num_load_family' => rand(1, 5),
                    'hourly_load' => rand(20, 40),
                    'contract_type' => rand(1, 4),
                    'service_start_year' => now()->year,
                    'unemployment_insurance' => true,
                    'retired' => false,
                    'base_salary' => rand(500000, 1000000),
                ];

                // Crear parámetros para el trabajador
                Parameter::insertParameters($worker->id, new \Illuminate\Http\Request($requestData), $schoolId);
            }
        }
    }
}
