<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Parameter;
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

        // Obtener el primer contador
        $contadorUser = User::where('role', User::CONTADOR)->first();

        // Verificar si se encontró un contador
        if ($contadorUser) {
            // Obtener el primer colegio asociado a este contador
            $schoolId = $contadorUser->schools->first()->id; // Obtener el primer colegio de los colegios asociados al contador

            // Crear varios trabajadores para ese colegio
            $workers = Worker::factory()->count(15)->create(['school_id' => $schoolId]);

            foreach ($workers as $worker) {
                // Generar el arreglo de horas de trabajo según el tipo de trabajador
                $loadHourlyWork = [];
                if ($worker->worker_type === Worker::WORKER_TYPE_TEACHER) {
                    // Si el trabajador es docente, asignamos horas aleatorias para cada día
                    $loadHourlyWork = [
                        'lunes' => rand(0, 8),
                        'martes' => rand(0, 8),
                        'miercoles' => rand(0, 8),
                        'jueves' => rand(0, 8),
                        'viernes' => rand(0, 8),
                        'sabado' => rand(0, 8),
                    ];
                } else {
                    // Si el trabajador es no docente, asignamos 0 horas para cada día
                    $loadHourlyWork = [
                        'lunes' => 0,
                        'martes' => 0,
                        'miercoles' => 0,
                        'jueves' => 0,
                        'viernes' => 0,
                        'sabado' => 0,
                    ];
                }

                // Convertimos el arreglo de horas de trabajo a JSON y lo asignamos
                $worker->load_hourly_work = json_encode($loadHourlyWork);
                // Guardamos los cambios en el trabajador (con las horas de trabajo)
                $worker->save();

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
                } 

                // Simular datos de la solicitud
                $requestData = [
                    'worker_type' => Worker::WORKER_TYPE_TEACHER,
                    'num_load_family' => rand(1, 5),
                    'hourly_load' => rand(20, 40),
                    'contract_type' => rand(1, 4),
                    'service_start_year' => now()->year,
                    'unemployment_insurance' => true,
                    'retired' => rand(1,0),
                    'base_salary' => rand(500000, 1000000),
                ];

                // Crear parámetros para el trabajador
                Parameter::insertParameters($worker->id, new \Illuminate\Http\Request($requestData), $schoolId);
            }
        }
    }
}