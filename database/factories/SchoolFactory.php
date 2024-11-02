<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Sustainer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition()
    {
        // Cargar las comunas y regiones desde la configuración
        $communes = config('communes_region.COMMUNE_OPTIONS');
        $regions = config('communes_region.REGIONES_OPTIONS');

        return [
            'rut' => $this->faker->unique()->numerify('##.###.###-#'),
            'name' => $this->faker->company . ' School',
            'rbd' => $this->faker->unique()->randomNumber(6),
            'address' => $this->faker->address,
            'commune' => $this->faker->randomElement($communes),
            'region' => $this->faker->randomElement($regions),
            'director' => $this->faker->name,
            'rut_director' => $this->faker->unique()->numerify('##.###.###-#'),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'dependency' => $this->faker->randomElement(array_keys(School::DEPENDENCY_OPTIONS)),
            'grantt' => $this->faker->randomElement(array_keys(School::GRANTT_OPTIONS)),
            'sustainer_id' => Sustainer::factory(), // Crea un sostenedor asociado
        ];
    }
}
