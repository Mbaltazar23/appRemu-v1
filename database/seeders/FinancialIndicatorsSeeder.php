<?php

namespace Database\Seeders;

use App\Models\Operation;
use App\Models\Parameter;
use App\Models\SchoolUser;
use App\Models\Tuition;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinancialIndicatorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        // Insertar los tres parámetros para cada escuela asociada a un contador
        $contadorUser = SchoolUser::first();
        // Verificar si se encontró un contador
        if ($contadorUser) {
            // Obtener todas las escuelas asociadas a los contadores
            $schoolId = $contadorUser->school_id; // Obtener el primer colegio de los colegios asociados al contador
            // Obtener un mes aleatorio entre 1 (Enero) y 12 (Diciembre)
            $randomMonth = rand(1, 12);

            // Determinar el valor de CIERREMES dependiendo del mes
            if ($randomMonth == 2) {
                $cierremes = 28; // Febrero tiene 28 días
            } else {
                $cierremes = rand(30, 31); // Los demás meses tienen 30 o 31 días
            }

            // Los otros dos parámetros se asignan de manera aleatoria
            $paramsToInsert = [
                'CIERREMES' => $cierremes, // Valor de ejemplo, puedes ajustar según lo que necesites
                'FACTORRBMNBASICA' => rand(100, 500), // Valor aleatorio entre 100 y 500
                'VALORIMD' => rand(1, 1000), // Valor aleatorio entre 1 y 1000
            ];

            // Insertar los tres parámetros en la tabla Parameter
            foreach ($paramsToInsert as $name => $value) {
                Parameter::updateOrCreate(
                    ['name' => $name, 'school_id' => $schoolId],
                    ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
                );
            }

            // Insertar valores en la tabla Parameter usando el factory para los impuesto_renta
            for ($i = 2; $i <= 8; $i++) {
                Parameter::factory()->create(['name' => "FACTORIMPTRAMO$i", 'description' => "Factor Impuesto tramo $i", 'value' => rand(1, 10), 'school_id' => $schoolId]);
                Parameter::factory()->create(['name' => "FACTORREBAJAIMPTRAMO$i", 'value' => rand(1, 5), 'school_id' => $schoolId]);
                Tuition::factory()->create(['tuition_id' => "FACTORIMPTRAMO$i", 'title' => "Factor Impuesto tramo $i", 'type' => 'P', 'school_id' => $schoolId]);
                Tuition::factory()->create(['tuition_id' => "FACTORREBAJAIMPTRAMO$i", 'title' => "Factor Rebaja Impuesto tramo $i", 'type' => 'P', 'school_id' => $schoolId]);
            }

            // Insertar valores en la tabla Operation usando el factory
            for ($i = 2; $i <= 8; $i++) {
                Operation::factory()->create([
                    'tuition_id' => "IMPUESTOTRAMO$i",
                    'operation' => 'REMUNERACIONTRIBUTABLE',
                    'limit_unit' => 'UTM',
                    'min_limit' => rand(1000, 5000),
                    'max_limit' => rand(6000, 10000),
                    'application' => 111111111111,
                    'school_id' => $schoolId,
                ]);
                Tuition::factory()->create([
                    'tuition_id' => "IMPUESTOTRAMO$i",
                    'title' => "IMPUESTOTRAMO$i",
                    'type' => 'O',
                    'school_id' => $schoolId,
                ]);
            }

            //Insertar valores en la tabla Operation usando el factory asignacion_familiar
            for ($i = 1; $i <= 3; $i++) {
                Parameter::factory()->create([
                    'name' => "FILTROASIGFAMT$i",
                    'description' => 'RENTAIMPONIBLE',
                    'value' => rand(1000, 5000),
                    'school_id' => $schoolId,
                ]);
                Parameter::factory()->create([
                    'name' => "ASIGCAR.FAMTRAMO$i",
                    'value' => rand(1000, 5000),
                    'school_id' => $schoolId,
                ]);
                Tuition::factory()->create([
                    'tuition_id' => "ASIGCAR.FAMTRAMO$i",
                    'title' => "Asignacion familiar tramo $i",
                    'type' => 'P',
                    'school_id' => $schoolId,
                ]);
                Operation::factory()->create([
                    'tuition_id' => "FILTROASIGFAMT$i",
                    'operation' => 'RENTAIMPONIBLE',
                    'min_limit' => rand(1000, 5000),
                    'max_limit' => rand(6000, 10000),
                    'application' => 111111111111,
                    'school_id' => $schoolId,
                ]);
            }
        }
    }

}
