<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tuition>
 */
class TuitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tuition_id' => $this->faker->unique()->word, // Genera un identificador único
            'title' => $this->faker->word, // Genera un título aleatorio
            'type' => $this->faker->randomElement(['P', 'O', 'S']), // Tipo aleatorio entre 'P', 'O', 'S'
            'description' => $this->faker->sentence, // Descripción aleatoria
            'in_liquidation' => 0, // Valor booleano
            'editable' => 0, // Valor booleano
            'school_id' => $this->faker->numberBetween(1, 10), // ID de la escuela aleatorio entre 1 y 10
        ];
    }
}
