<?php

namespace Database\Factories;

use App\Models\Operation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Operation>
 */
class OperationFactory extends Factory
{
    
    protected $model = Operation::class;

    public function definition()
    {
        return [
            'tuition_id' => $this->faker->unique()->word,
            'min_limit' => $this->faker->numberBetween(1000, 5000),
            'max_limit' => $this->faker->numberBetween(6000, 10000),
        ];
    }
}
