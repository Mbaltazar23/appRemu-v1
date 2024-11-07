<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los usuarios con el rol 'Contador'
        $contadores = User::where('role', User::CONTADOR)->get();

        // Verificar si existen usuarios con el rol 'Contador'
        if ($contadores->isNotEmpty()) {
            // Asociar cada 'Contador' con una escuela de forma masiva
            $contadores->each(function ($contador) {
                $school = School::inRandomOrder()->first(); // Obtener una escuela aleatoria
                // Crear el registro en la tabla pivote 'school_user' para cada 'Contador'
                if ($school) {
                    // Usar el factory para crear el registro en la tabla pivote 'school_user'
                    SchoolUser::factory()->create([
                        'user_id' => $contador->id,
                        'school_id' => $school->id,
                    ]);
                }
            });
        }
    }
}
