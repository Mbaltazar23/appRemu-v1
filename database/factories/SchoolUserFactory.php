<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\SchoolUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolUser>
 */
class SchoolUserFactory extends Factory
{
    protected $model = SchoolUser::class;

    public function definition()
    {
        return [
            // Seleccionamos un usuario con el rol 'Contador' de manera aleatoria
            'user_id' => User::where('role', User::CONTADOR)->inRandomOrder()->first()->id, // Filtra solo los 'Contadores'

            // Seleccionamos una escuela de manera aleatoria
            'school_id' => School::inRandomOrder()->first()->id, 
        ];
    }
}
