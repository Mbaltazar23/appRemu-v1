<?php
namespace Database\Factories;

use App\Models\Role;
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
            // We select a user with the role 'Accountant' at random
            'user_id'   => User::where('role_id', Role::where('name', 'Contador')->value('id'))->inRandomOrder()->first()->id,
            // Filter only the 'Contadores'
            // We select a school at random
            'school_id' => School::inRandomOrder()->first()->id,
        ];
    }
}
