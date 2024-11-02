<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el Super Admin con todos los permisos
        User::factory()->create([
            'name' => 'Super User',
            'email' => 'admin@mail.com',
            'role' => User::SUPER_ADMIN,
            'permissions' => array_keys(User::getPermissions()), // Almacena directamente el array como JSON
        ]);

        // Permisos para el Contador
        $permissionsContador = [
            'MANBODESCOL',
            'PLANREMU',
            'MANISAPRE',
        ];

        // Crear el usuario Contador
        User::factory()->create([
            'name' => 'Contador User',
            'email' => 'contador@mail.com',
            'role' => User::CONTADOR,
            'permissions' => array_intersect_key(config('permissions'), array_flip($permissionsContador)), // Almacena directamente el array como JSON
        ]);

        // Crear el usuario Sostenedor
        User::factory()->create([
            'name' => 'Sostenedor User',
            'email' => 'sostenedor@mail.com',
            'role' => User::SOSTENEDOR,
        ]);

        // Crear más usuarios adicionales
        $additionalUsersCount = 10; // Cantidad de usuarios adicionales a crear
        User::factory()->count($additionalUsersCount)->create();
    }
}
