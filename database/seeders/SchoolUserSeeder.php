<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asociar el Contador con un colegio
        $contador = User::where('role', User::CONTADOR)->first();
        $schoolForContador = School::inRandomOrder()->first();
        if ($contador && $schoolForContador) {
            SchoolUser::create([
                'user_id' => $contador->id,
                'school_id' => $schoolForContador->id,
            ]);
        }

        // Asociar usuarios adicionales a colegios
        $additionalUsers = User::whereNotIn('role', [User::SUPER_ADMIN, User::CONTADOR, User::SOSTENEDOR])->get();
        foreach ($additionalUsers as $user) {
            $school = School::inRandomOrder()->first();
            if ($school) {
                SchoolUser::create([
                    'user_id' => $user->id,
                    'school_id' => $school->id,
                ]);
            }
        }
    }
}
