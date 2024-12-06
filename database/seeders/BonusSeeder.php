<?php

namespace Database\Seeders;

use App\Models\Bonus;
use App\Models\User;
use Illuminate\Database\Seeder;

class BonusSeeder extends Seeder
{
    public function run()
    {
        // Obtener el primer contador
        $contadorUser = User::where('role', User::CONTADOR)->first();
        // Verificar si se encontró un contador
        if ($contadorUser) {
            // Obtener el primer colegio asociado a este contador
            $schoolId = $contadorUser->schools->first()->id; // Obtener el primer colegio de los colegios asociados al contador
            // Generamos una vez los bonos
            $bonuses = $this->generateBonuses($schoolId);
            // Insertamos los bonos para cada escuela
            foreach ($bonuses as $bonusData) {
                // Añadimos el school_id al bono antes de guardarlo
                Bonus::processCreateBonuses($bonusData);
            }
        }
    }
    /**
     * Genera todos los bonos, solo una vez.
     * @return array
     */
    private function generateBonuses($school_id)
    {
        return [
            [
                'title' => 'Perfeccionamiento',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'D',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 0,
            ],
            [
                'title' => 'Ley 19410',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 80,
                'application' => 'H',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 795260,
            ],
            [
                'title' => 'Prestamo Social Caja los heroes',
                'type' => 3,
                'taxable' => 0,
                'imputable' => 1,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'D',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 0,
            ],
            [
                'title' => 'Prestamo Social Caja los Andes',
                'type' => 3,
                'taxable' => 0,
                'imputable' => 1,
                'is_bonus' => 1,
                'factor' => 100,
                'application' => 'D',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 0,
            ],
            [
                'title' => 'Colegio de profesores',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 1,
                'is_bonus' => 1,
                'factor' => 1,
                'application' => 'I',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 0,
            ],
            [
                'title' => 'Cuenta Ahorro Caja los Andes',
                'type' => 3,
                'taxable' => 0,
                'imputable' => 1,
                'is_bonus' => 1,
                'factor' => 100,
                'application' => 'D',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 0,
            ],
            [
                'title' => 'Fundacion Lopez Perez',
                'type' => 3,
                'taxable' => 0,
                'imputable' => 1,
                'is_bonus' => 1,
                'factor' => 100,
                'application' => 'D',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 0,
            ],
            [
                'title' => 'Desempeño dificil',
                'type' => 3,
                'taxable' => 0,
                'imputable' => 1,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'H',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 466607,
            ],
            [
                'title' => 'Ley 19933',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 80,
                'application' => 'H',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 2057449,
            ],
            [
                'title' => 'Otros',
                'type' => 2,
                'taxable' => 1,
                'imputable' => 1,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'D',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 0,
            ],
            [
                'title' => 'Ley 19464',
                'type' => 2,
                'taxable' => 0,
                'imputable' => 1,
                'is_bonus' => 0,
                'factor' => 1,
                'application' => 'H',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 246661,
            ],
            [
                'title' => 'RBMN',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'C',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 7824,
            ],
            [
                'title' => 'UMP',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'T',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 663,
            ],
			/*[
                'title' => 'Todos por igual',
                'type' => 2,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 1,
                'application' => 'F',
                'months' => $this->generateDynamicMonths([1,2,3,4,5,6,7,8,9,10,11,12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
            ],*/
        ];
    }

    private function generateDynamicMonths($selectedMonths = null)
    {
        // Si no se pasa un arreglo con meses específicos, generamos un rango de meses por defecto (por ejemplo, de 2 a 11)
        if ($selectedMonths === null) {
            $selectedMonths = range(2, 11); // Genera un arreglo de [2, 3, 4, ..., 11]
        }

        // Convertir los números de meses en strings, si es necesario (aunque PHP manejaría correctamente los enteros)
        $selectedMonths = array_map('strval', $selectedMonths);

        // Ahora, tenemos un arreglo de los meses seleccionados, por ejemplo ["2", "3", "4", ..., "11"]
        return $selectedMonths;
    }
}
