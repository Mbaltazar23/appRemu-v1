<?php

namespace Database\Factories;

use App\Models\Absence;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Absence>
 */
class AbsenceFactory extends Factory {

    protected $model = Absence::class;

    public function definition() {
        // Seleccionamos un trabajador aleatorio
        $worker = Worker::inRandomOrder()->first(); // Seleccionamos un trabajador aleatorio
        // Fecha aleatoria para la ausencia
        $date = $this->faker->dateTimeThisYear(); // Obtiene una fecha aleatoria dentro de este año
        $day = $date->format('d');
        $month = $date->format('m');
        $year = $date->format('Y');

        // Motivo aleatorio para la ausencia
        $reasons = ['Enfermedad', 'Vacaciones', 'Permiso personal', 'Falta injustificada'];
        $reason = $this->faker->randomElement($reasons);

        return [
            'worker_id' => $worker->id,
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'reason' => $reason,
            'minutes' => $this->faker->numberBetween(30, 480), // Duración aleatoria de la ausencia en minutos (de 30 min a 8 horas)
            'with_consent' => $this->faker->boolean(), // Si la ausencia tiene consentimiento
        ];
    }

}
