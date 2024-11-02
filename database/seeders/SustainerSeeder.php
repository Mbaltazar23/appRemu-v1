<?php

namespace Database\Seeders;

use App\Models\Sustainer;
use Illuminate\Database\Seeder;

class SustainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sustainer::factory()->count(10)->create();
    }
}
