<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permissionsAdmin = [
            'MANUSU',
            'VERHISTORIAL',
            'MANSOST',
            'CCOST',
        ];

        $permissionsAdminKeys = array_intersect_key(config('permissions'), array_flip($permissionsAdmin));


        // Crear el Super Admin con todos los permisos
        User::factory()->create([
            'name' => 'Super User',
            'email' => 'admin@mail.com',
            'role' => User::SUPER_ADMIN,
            'permissions' => array_keys($permissionsAdminKeys), // Almacena directamente el array como JSON
        ]);
        // Permisos para el Contador (se seleccionan solo las claves de permisos específicos)
        $permissionsContador = [
            'MANTRA',
            'MANBODESCOL',
            'PLANREMU',
            'MANISAPRE',
            'MANAFP',
            'MANLIC',
            'MANIECO',
            'MANINAS'
        ];

// Obtener solo las claves de permisos para el Contador
        $permissionsContadorKeys = array_intersect_key(config('permissions'), array_flip($permissionsContador));

// Crear el usuario Contador con los permisos seleccionados
        User::factory()->create([
            'name' => 'Contador User',
            'email' => 'contador@mail.com',
            'role' => User::CONTADOR,
            'permissions' => array_keys($permissionsContadorKeys), // Solo almacenamos las claves
        ]);

        // Crear el usuario Sostenedor
        User::factory()->create([
            'name' => 'Sostenedor User',
            'email' => 'sostenedor@mail.com',
            'role' => User::SOSTENEDOR,
            'permissions' => array_keys($permissionsContadorKeys), // Solo almacenamos las claves
        ]);

        // Crear más usuarios adicionales
        $additionalUsersCount = 15; // Cantidad de usuarios adicionales a crear
        User::factory()->count($additionalUsersCount)->create();
    }
}
