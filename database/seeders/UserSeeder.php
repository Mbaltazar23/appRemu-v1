<?php

namespace Database\Seeders;

use App\Models\Role;
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
            'MANPER',
            'VERHISTORIAL',
            'MANSOST',
            'CCOST',
        ];

        $permissionsContador = [
            'MANTRA',
            'MANBODESCOL',
            'PLANREMU',
            'MANISAPRE',
            'MANAFP',
            'MANLIC',
            'MANIECO',
            'MANINAS',
        ];

        // Crear roles
        $superAdminRole = Role::create([
            'name' => 'Super Admin',
            'permissions' => array_keys(config('permissions')),
        ]);

        $adminRole = Role::create([
            'name' => 'Administrador',
            'permissions' => $permissionsAdmin,
        ]);

        $contadorRole = Role::create([
            'name' => 'Contador',
            'permissions' => $permissionsContador,
        ]);

        $sostenedorRole = Role::create([
            'name' => 'Sostenedor',
            'permissions' => [],
        ]);

        $usuarioRole = Role::create([
            'name' => 'Usuario',
            'permissions' => [],
        ]);

        // Crear usuarios
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mail.com',
            'role_id' => $superAdminRole->id, // Asigna por ID
        ]);

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@mail.com',
            'role_id' => $adminRole->id,
        ]);

        User::factory()->create([
            'name' => 'Contador User',
            'email' => 'contador@mail.com',
            'role_id' => $contadorRole->id,
        ]);

        User::factory()->create([
            'name' => 'Sostenedor User',
            'email' => 'sostenedor@mail.com',
            'role_id' => $sostenedorRole->id,
        ]);

        User::factory(5)->create([
            'role_id' => $usuarioRole->id,
        ]);
    }
}
