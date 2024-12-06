<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Parameter;
use App\Models\User;
use App\Models\Worker;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use NumberFormatter;

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

            // Seleccionar aleatoriamente un trabajador como titular
            $titularWorker = $workers->random(); // Selecciona uno aleatoriamente

            foreach ($workers as $worker) {
                // Asignar aleatoriamente un tipo de trabajador (Docente o No Docente)
                $workerType = rand(Worker::WORKER_TYPE_TEACHER, Worker::WORKER_TYPE_NON_TEACHER); // 1 = Docente, 2 = No Docente
                $worker->worker_type = $workerType;
                $worker->worker_titular = $titularWorker->id; // Marca el trabajador seleccionado como titular
                $worker->save();

                // Generar el arreglo de horas de trabajo según el tipo de trabajador
                $loadHourlyWork = [];
                // Generar el arreglo de horas de trabajo según el tipo de trabajador
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
                    // Si el trabajador no es docente, asignamos 0 horas para cada día
                    $loadHourlyWork = Worker::createHourlyLoadArray(new Request()); // Usamos el método para generar el arreglo
                }

// Actualizamos la carga horaria del trabajador usando el método del modelo
                $worker->updateHourlyLoad($loadHourlyWork);

                // Crear un contrato para cada trabajador
                $contract = Contract::create([
                    'worker_id' => $worker->id,
                    'contract_type' => array_rand(Contract::CONTRACT_TYPES),
                    'hire_date' => now()->toDateString(),
                    'termination_date' => now()->addYear()->toDateString(),
                    'replacement_reason' => null,
                ]);

                // Decidir aleatoriamente si crear detalles del contrato
                if (rand(0, 1) === 1) { // Si el valor es 1, crear detalles
                    // Generación de los detalles del contrato
                    $totalRemuneration = rand(400000, 1200000); // Generamos un valor de remuneración
                    $details = [
                        'city' => $faker->city,
                        'duration' => array_rand(Contract::DURATION_OPTIONS),
                        'total_remuneration' => $totalRemuneration,
                        'remuneration_gloss' => $this->convertNumberToWords($totalRemuneration), // Convertir el valor de total_remuneration a texto
                        'origin_city' => $faker->city,
                        'schedule' => array_rand(Contract::SCHEDULE_OPTIONS),
                    ];

                    // Solo si el worker_type es 1 (Docente), agregamos 'levels' al detalle
                    if ($worker->worker_type === Worker::WORKER_TYPE_TEACHER) {
                        $details['levels'] = array_rand(Contract::LEVELS_OPTIONS);
                    }

                    // Actualizamos los detalles del contrato
                    $contract->update(['details' => json_encode($details)]);
                }

                // Simular datos de la solicitud
                $requestData = [
                    'worker_type' => $worker->worker_type, // Se pasa el worker_type aleatorio
                    'num_load_family' => rand(1, 5),
                    'hourly_load' => rand(20, 40),
                    'contract_type' => rand(1, 4),
                    'service_start_year' => now()->year,
                    'unemployment_insurance' => true,
                    'retired' => rand(1, 0),
                    // Asignar base salary solo si es trabajador no docente (worker_type = 2)
                    'base_salary' => $worker->worker_type === Worker::WORKER_TYPE_NON_TEACHER ? rand(500000, 1000000) : null,
                ];

                // Crear parámetros para el trabajador
                Parameter::insertParameters($worker->id, new Request($requestData), $schoolId);
            }
        }
    }

    private function convertNumberToWords($number)
    {
        $formatter = new NumberFormatter('es_ES', NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($number));
    }
}
