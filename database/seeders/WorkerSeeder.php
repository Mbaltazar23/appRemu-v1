<?php
namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Parameter;
use App\Models\School;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asociar solo usuarios "Contador" con colegios
        $contadorUsers = User::where('role', User::CONTADOR)->get();

        foreach ($contadorUsers as $user) {
            // Obtener un colegio aleatorio
            $schoolId = School::inRandomOrder()->first()->id;

            // Crear varios trabajadores
            $workers = Worker::factory()->count(5)->create(['school_id' => $schoolId]);

            foreach ($workers as $worker) {
                // Crear un contrato para cada trabajador
                Contract::create([
                    'worker_id' => $worker->id,
                    'contract_type' => rand(1, 4),
                    'hire_date' => now()->toDateString(),
                    'termination_date' => now()->addYear()->toDateString(),
                    'replacement_reason' => null,
                ]);

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
