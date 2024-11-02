<?php

namespace Database\Seeders;

use App\Models\Operation;
use App\Models\Parameter;
use Illuminate\Database\Seeder;

class FinancialIndicatorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        // Insertar valores en la tabla Parameter usando el factory
        for ($i = 2; $i <= 8; $i++) {
            Parameter::factory()->create(['name' => "FACTORIMPTRAMO$i", 'value' => rand(1, 10)]);
            Parameter::factory()->create(['name' => "FACTORREBAJAIMPTRAMO$i", 'value' => rand(1, 5)]);
        }

        // Insertar valores en la tabla Operation usando el factory
        for ($i = 2; $i <= 8; $i++) {
            Operation::factory()->create([
                'tuition_id' => "IMPUESTOTRAMO$i",
                'min_limit' => rand(1000, 5000),
                'max_limit' => rand(6000, 10000),
            ]);
        }
    }

}
