<?php

namespace Database\Seeders;

use App\Models\Bonus;
use App\Models\Operation;
use App\Models\SchoolUser;
use App\Models\Tuition;
use Illuminate\Database\Seeder;

class BonusSeeder extends Seeder
{
    public function run()
    {
        // Obtener el primer contador
        $contadorUser = SchoolUser::first();
        // Verificar si se encontró un contador
        if ($contadorUser) {
            // Obtener el primer colegio asociado a este contador
            $schoolId = $contadorUser->school_id;
            // Generamos una vez los bonos
            $bonuses = $this->generateBonuses($schoolId);
            // Insertamos los bonos para cada escuela
            foreach ($bonuses as $bonusData) {
                // Añadimos el school_id al bono antes de guardarlo
                Bonus::processCreateBonuses($bonusData);
            }
            $this->OperationsRelationsForBonuses($schoolId);
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
                'title' => 'Ley 19410',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 80,
                'application' => 'H',
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 795260,
            ],
            [
                'title' => 'Ley 19933',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 80,
                'application' => 'H',
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 2231533,
            ],
            [
                'title' => 'Perfeccionamiento',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'D',
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 0,
            ],
            [
                'title' => 'Prestamo Social Caja los heroes',
                'type' => 3,
                'taxable' => 0,
                'imputable' => 1,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'D',
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
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
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
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
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
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
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
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
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
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
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 466607,
            ],
            [
                'title' => 'Otros',
                'type' => 2,
                'taxable' => 1,
                'imputable' => 1,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'D',
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 0,
            ],
            [
                'title' => 'Ley 19464',
                'type' => 2,
                'taxable' => 0,
                'imputable' => 1,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'H',
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 246661,
            ],
            [
                'title' => 'UMP',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'T',
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 663,
            ],
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

    private function OperationsRelationsForBonuses($schoolId)
    {
        $tuitionLey19410Id = Tuition::where('title', 'Valor Ley 19410')->value('tuition_id');
        $tuitionLey19933Id = Tuition::where('title', 'Valor Ley 19933')->value('tuition_id');
        $AplicacionLey19933Id = Tuition::where('title', 'Aplicación de Ley 19933')->value('tuition_id');
        $AplicacionLey19410Id = Tuition::where('title', 'Aplicación de Ley 19410')->value('tuition_id');
        $tuitionUmp = Tuition::where('title', 'Valor UMP')->value('tuition_id');
        $factorRBMN  = Tuition::where('title', 'Valor RBMN')->where('school_id', $schoolId)->first()->tuition_id;

        
        $operations = [
            ['EXCEDENTEBONOSAELEY19410Y19933', 1, "$tuitionLey19410Id + $tuitionLey19933Id / 0.8 * 12 * 0.2 * CARGAHORARIA / SUMACARGAS", 0, 0, 0, '000000000000'],
            ['PLANILLACOMPLEMENTARIA', 1,
                "VALORIMD M+ $factorRBMN M- $tuitionUmp M- $tuitionLey19933Id * $AplicacionLey19933Id / SUMACARGAS M- $tuitionLey19410Id * $AplicacionLey19410Id / SUMACARGAS M- MR * CARGAHORARIA * FACTORASIST",
             0, 0, 0, '111111111111'],
        ];

        foreach ($operations as $operation) {
            Operation::create([
                'tuition_id' => $operation[0], // Asignamos el tuition_id de acuerdo al título encontrado
                'worker_type' => $operation[1],
                'operation' => $operation[2],
                'min_limit' => $operation[3],
                'max_limit' => $operation[4],
                'max_value' => $operation[5],
                'application' => $operation[6],
                'school_id' => $schoolId,
            ]);
        }
    }
}
