<?php

namespace Database\Factories;

use App\Models\Insurance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Insurance>
 */
class InsuranceFactory extends Factory
{
    protected $model = Insurance::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement([Insurance::AFP, Insurance::ISAPRE, Insurance::FONASA]),
            'cotizacion' => $this->faker->randomFloat(2, 5, 15), // Cotización entre 5 y 15
            'rut' => $this->faker->unique()->numerify('##.###.###-#'), // RUT ficticio
        ];
    }
}
