<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Parameter;
use App\Models\SchoolUser;
use App\Models\Worker;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use App\Helpers\ConvertNumberToWords;
use App\Models\Tuition;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $contadorUser = SchoolUser::first();

        if ($contadorUser) {
            $schoolId = $contadorUser->school_id;
            $workers = Worker::factory()->count(5)->create(['school_id' => $schoolId]);
            $titularWorker = $workers->random();

            foreach ($workers as $worker) {
                $this->assignTitularWorker($worker, $titularWorker);
                $hourlyLoad = $this->generateHourlyLoad();
                $loadHourlyWork = $this->distributeHourlyLoad($hourlyLoad);
                $requestData = $this->generateRequestData($worker, $hourlyLoad, $schoolId);

                Parameter::insertParameters($worker->id, new Request($requestData), $schoolId);
                $contract = $this->createContract($worker);
                $worker->createHourlyLoadArray(new Request($loadHourlyWork));

                if (rand(0, 1) === 1) {
                    $this->createContractDetails($worker, $contract, $hourlyLoad, $requestData, $faker);
                }
            }
        }
    }

    /**
     * Assign a worker as titular (main worker).
     */
    private function assignTitularWorker(Worker $worker, $titularWorker)
    {
        $worker->worker_titular = $titularWorker->id;
        $worker->save();
    }

    /**
     * Generate a random hourly load (between 20 and 45).
     */
    private function generateHourlyLoad()
    {
        return rand(20, 45);
    }

    /**
     * Distribute the hourly load across the days of the week.
     */
    private function distributeHourlyLoad($hourlyLoad)
    {
        $loadHourlyWork = [];
        if ($hourlyLoad === 40 || $hourlyLoad === 35) {
            $dailyLoad = intdiv($hourlyLoad, 5);
            $remainingHours = $hourlyLoad - ($dailyLoad * 5);

            $loadHourlyWork = [
                'carga_lunes' => $dailyLoad,
                'carga_martes' => $dailyLoad,
                'carga_miercoles' => $dailyLoad,
                'carga_jueves' => $dailyLoad,
                'carga_viernes' => $dailyLoad,
                'carga_sabado' => $remainingHours,
            ];
        } else {
            $remainingHours = $hourlyLoad;
            for ($i = 0; $i < 5; $i++) {
                $day = ['carga_lunes', 'carga_martes', 'carga_miercoles', 'carga_jueves', 'carga_viernes'][$i];
                $loadHourlyWork[$day] = floor($remainingHours / (6 - $i));
                $remainingHours -= $loadHourlyWork[$day];
            }
            $loadHourlyWork['carga_sabado'] = $remainingHours;
        }

        return $loadHourlyWork;
    }

    /**
     * Generate request data for the worker.
     */
    private function generateRequestData(Worker $worker, $hourlyLoad, $schoolId)
    {
        return [
            'worker_type' => $worker->worker_type,
            'num_load_family' => rand(1, 5),
            'hourly_load' => $hourlyLoad,
            'contract_type' => rand(1, 4),
            'service_start_year' => now()->year,
            'unemployment_insurance' => rand(1, 0),
            'retired' => rand(1, 0),
            'base_salary' => $worker->worker_type == Worker::WORKER_TYPE_NON_TEACHER
                ? rand(500000, 600000)
                : $this->generateRemuneration($schoolId, $hourlyLoad),
        ];
    }

    /**
     * Create a contract for the worker.
     */
    private function createContract(Worker $worker)
    {
        return Contract::create([
            'worker_id' => $worker->id,
            'contract_type' => array_rand(Contract::CONTRACT_TYPES),
            'hire_date' => now()->toDateString(),
            'termination_date' => now()->addYear()->toDateString(),
            'replacement_reason' => "",
        ]);
    }

    /**
     * Create contract details if required.
     */
    private function createContractDetails(Worker $worker, $contract, $hourlyLoad, $requestData, $faker)
    {
        $details = [
            'city' => $faker->city,
            'levels' => array_rand(Contract::LEVELS_OPTIONS),
            'duration' => Contract::CONTRACT_TYPES[$contract->contract_type],
            'total_remuneration' => $requestData['base_salary'],
            'remuneration_gloss' => ConvertNumberToWords::convert($requestData['base_salary']),
            'origin_city' => $faker->city,
            'schedule' => array_rand(Contract::SCHEDULE_OPTIONS),
            'teaching_hours' => $worker->worker_type === Worker::WORKER_TYPE_TEACHER ? $hourlyLoad : '',
            'curricular_hours' => $worker->worker_type === Worker::WORKER_TYPE_NON_TEACHER ? $hourlyLoad : '',
        ];

        $contract->update(['details' => json_encode($details)]);
    }

    /**
     * Generate remuneration for a non-teacher worker based on hourly load.
     */
    private function generateRemuneration($school_id, $hourlyWork)
    {
        $rbnmTuituion = Tuition::where('title', "Valor RBMN")->value('tuition_id');
        return Parameter::getParameterValue($rbnmTuituion, 0, $school_id) * $hourlyWork;
    }
}
