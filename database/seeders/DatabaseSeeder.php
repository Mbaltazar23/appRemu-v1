<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(SustainerSeeder::class);
        $this->call(SchoolSeeder::class);
        $this->call(SchoolUserSeeder::class);
        $this->call(InsuranceSeeder::class);
        $this->call(WorkerSeeder::class);
        $this->call(OperationsTableSeeder::class);
        $this->call(TuitionsTableSeeder::class);
        $this->call(FinancialIndicatorsSeeder::class);
        $this->call(BonusSeeder::class);
        $this->call(LicenseSeeder::class);
        $this->call(AbsenceSeeder::class);
        $this->call(TemplateSeeder::class);
    }
}
