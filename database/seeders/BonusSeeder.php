<?php

namespace Database\Seeders;

use App\Models\Bonus;
use App\Models\Operation;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class BonusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Filtramos usuarios con el rol de "Contador" que tengan al menos una escuela asociada
        $user = User::where('role', User::CONTADOR) // Filtramos solo por rol "Contador"
            ->whereHas('schools') // Aseguramos que tenga al menos una escuela asociada
            ->inRandomOrder() // Seleccionamos un usuario aleatorio
            ->first();

        // Si no se encuentra un usuario contador con escuela, se asigna un valor predeterminado
        if (!$user) {
            // Puede lanzar un error o asignar un usuario/escuela predeterminado
            $user = User::first(); // Seleccionamos el primer usuario (esto debería manejarse mejor)
        }

        // Seleccionamos aleatoriamente una escuela asociada al usuario
        $school = $user->schools()->inRandomOrder()->first();

        // Si no tiene escuelas asociadas, asignamos la primera escuela en la base de datos
        if (!$school) {
            $school = School::first();
        }

        // Crear 15 bonos utilizando el método `processCreateBonuses`
        for ($i = 0; $i < 10; $i++) {
            // Generar entre 1 y 6 meses seleccionados aleatoriamente para cada bono
            // Seleccionar entre 1 y 6 meses aleatorios (sin usar Faker)
            $numMonths = rand(1, 6); // Cantidad de meses seleccionados aleatoriamente
            $selectedMonths = array_rand(range(0, 11), $numMonths); // Selección aleatoria de meses (índices 0 a 11)

            // Asegurarse de que $selectedMonths sea un array
            if (!is_array($selectedMonths)) {
                $selectedMonths = [$selectedMonths];
            }

            // Inicializar un array de 12 elementos con '0' (meses no seleccionados)
            $months = array_fill(0, 12, 0);

            // Decidir aleatoriamente si quieres un patrón específico o todos los meses seleccionados con 1
            $randomChoice = rand(0, 1); // 0 para patrón aleatorio, 1 para todos 1s

            if ($randomChoice === 1) {
                // Llenar todos los meses con 1
                $months = array_fill(0, 12, 1);
            } else {
                // Generar un patrón aleatorio de 12 cifras (con 0s y 1s)
                $pattern = '';
                for ($i = 0; $i < 12; $i++) {
                    $pattern .= rand(0, 1); // Generar un 0 o 1 aleatoriamente
                }

                // Convertir el patrón en un array de 12 elementos
                $months = str_split($pattern);
            }

// Si eliges la opción de seleccionar aleatoriamente los meses, lo manejas aquí
            if ($randomChoice === 0) {
                // Poner '1' en los meses seleccionados
                foreach ($selectedMonths as $month) {
                    $months[$month] = 1; // El mes seleccionado se marca con '1'
                }
            }
            // Generación aleatoria de los otros datos
            $title = 'Bono-' . \Faker\Factory::create()->word . '-' . rand(10000, 99999); // Título aleatorio
            $type = rand(Operation::WORKER_TYPE_TEACHER, Operation::WORKER_TYPE_ALL); // 1: Docente, 2: No Docente, 3: Todos
            $is_bonus = rand(0, 1); // Bono o descuento
            // Obtener las claves de application_options y seleccionar una aleatoria (solo la letra)
            $application = array_rand(Bonus::APPLICATION_OPTIONS); // Selección aleatoria de opción
            $factor = rand(50, 100) / 100; // Factor entre 0.5 y 1 (50% a 100%)
            $imputable = rand(0, 1); // Si es imputable o no
            $taxable = rand(0, 1); // Si es imponible
            $amount = rand(100, 1000); // Monto entre 100 y 1000

            // Usamos el método `processCreateBonuses` para crear el bono
            Bonus::processCreateBonuses([
                'title' => $title,
                'school_id' => $school->id,
                'type' => $type,
                'is_bonus' => $is_bonus,
                'application' => $application,
                'factor' => $factor,
                'taxable' => $taxable,
                'imputable' => $imputable,
                'months' => $months, // La cadena con los meses seleccionados
                'amount' => $amount,
            ]);
        }
    }
}
