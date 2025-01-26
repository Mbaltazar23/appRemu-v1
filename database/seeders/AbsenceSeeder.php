<?php

namespace Database\Seeders;

use App\Models\Absence;
use Illuminate\Database\Seeder;

class AbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Absence::factory()->count(10)->create();
    }
}
