<?php

namespace Database\Factories;

use App\Models\Sustainer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sustainer>
 */
class SustainerFactory extends Factory
{
    protected $model = Sustainer::class;

    public function definition()
    {
        // Cargar las comunas y regiones desde la configuración
        $communes = config('communes_region.COMMUNE_OPTIONS');
        $regions = config('communes_region.REGIONES_OPTIONS');

        return [
            'rut' => $this->faker->unique()->numerify('##.###.###-#'),
            'business_name' => $this->faker->company,
            'address' => $this->faker->address,
            'commune' => $this->faker->randomElement($communes),
            'region' => $this->faker->randomElement($regions),
            'legal_nature' => $this->faker->word,
            'legal_representative' => $this->faker->name,
            'rut_legal_representative' => $this->faker->unique()->numerify('##.###.###-#'),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
