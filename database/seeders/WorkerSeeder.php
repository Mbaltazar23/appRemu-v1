<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Parameter;
use App\Models\SchoolUser;
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
        $contadorUser = SchoolUser::first();
        // Verificar si se encontró un contador
        if ($contadorUser) {
            // Obtener el primer colegio asociado a este contador
            $schoolId = $contadorUser->school_id; // Obtener el primer colegio de los colegios asociados al contador
            // Crear varios trabajadores para ese colegio
            $workers = Worker::factory()->count(15)->create(['school_id' => $schoolId]);
            // Seleccionar aleatoriamente un trabajador como titular
            $titularWorker = $workers->random(); // Selecciona uno aleatoriamente
            // procedemos a recorrer el arreglo de trabajadores que se crearan
            foreach ($workers as $worker) {
                // Asignar aleatoriamente un tipo de trabajador (Docente o No Docente)
                $workerType = rand(Worker::WORKER_TYPE_TEACHER, Worker::WORKER_TYPE_NON_TEACHER); // 1 = Docente, 2 = No Docente
                $worker->worker_type = $workerType;
                $worker->worker_titular = $titularWorker->id; // Marca el trabajador seleccionado como titular
                $worker->save();
                // Generar el arreglo de horas de trabajo según el tipo de trabajador
                $loadHourlyWork = [];
                $hourlyLoad = rand(20, 45); // Carga horaria aleatoria entre 20 y 45 horas
                // Si el trabajador es docente, asignamos horas aleatorias para cada día
                if ($hourlyLoad === 40 || $hourlyLoad === 35) {
                    // Repartir 40 o 35 horas entre lunes a viernes
                    $dailyLoad = intdiv($hourlyLoad, 5); // Dividimos las horas entre 5 días (lunes a viernes)
                    $remainingHours = $hourlyLoad - ($dailyLoad * 5); // Calcular el remanente
                    // Distribuir las horas
                    $loadHourlyWork = [
                        'lunes' => $dailyLoad,
                        'martes' => $dailyLoad,
                        'miercoles' => $dailyLoad,
                        'jueves' => $dailyLoad,
                        'viernes' => $dailyLoad,
                        'sabado' => $remainingHours, // Las horas restantes van al sábado
                    ];
                } else {
                    // Para otros valores de `hourly_load` menores a 35, asignamos de forma proporcional
                    $remainingHours = $hourlyLoad; // Empezamos con todo el total de horas
                    // Intentamos repartir proporcionalmente entre lunes a viernes (siempre dejando el sábado con menos)
                    for ($i = 0; $i < 5; $i++) {
                        $day = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'][$i];
                        $loadHourlyWork[$day] = floor($remainingHours / (6 - $i)); // Distribuir entre los días restantes
                        $remainingHours -= $loadHourlyWork[$day]; // Reducir el remanente
                    }
                    $loadHourlyWork['sabado'] = $remainingHours; // Lo que queda va al sábado
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
                        'remuneration_gloss' => $this->convertNumberToWords($totalRemuneration),
                        'origin_city' => $faker->city,
                        'schedule' => array_rand(Contract::SCHEDULE_OPTIONS),
                        'teaching_hours' => $worker->worker_type === Worker::WORKER_TYPE_TEACHER ? $hourlyLoad : '',
                        'curricular_hours' => $worker->worker_type === Worker::WORKER_TYPE_NON_TEACHER ? $hourlyLoad : '',
                    ];
                    // Solo si el worker_type es 1 (Docente), agregamos 'levels' al detalle
                    if ($worker->worker_type === Worker::WORKER_TYPE_TEACHER) {
                        $details['levels'] = array_rand(Contract::LEVELS_OPTIONS);
                    }else{
                        $details['levels'] = '';
                    }
                    // Actualizamos los detalles del contrato
                    $contract->update(['details' => json_encode($details)]);
                }
                // Simular datos de la solicitud
                $requestData = [
                    'worker_type' => $worker->worker_type, // Se pasa el worker_type aleatorio
                    'num_load_family' => rand(1, 5),
                    'hourly_load' => $hourlyLoad, // Usamos el valor calculado de hourly_load
                    'contract_type' => rand(1, 4),
                    'service_start_year' => now()->year,
                    'unemployment_insurance' => true,
                    'retired' => rand(1, 0),
                    'base_salary' => $worker->worker_type === Worker::WORKER_TYPE_NON_TEACHER ?? rand(500000, 1000000),          
                    // Asignar base salary solo si es trabajador no docente (worker_type = 2)
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
