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
            'user_id' => User::inRandomOrder()->first()->id, // Selecciona un usuario existente
            'school_id' => School::inRandomOrder()->first()->id, // Selecciona un colegio existente
        ];
    }
}
