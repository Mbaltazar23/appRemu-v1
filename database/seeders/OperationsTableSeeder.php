<?php
namespace Database\Seeders;

use App\Models\Bonus;
use App\Models\Operation;
use App\Models\SchoolUser;
use App\Models\Tuition;
use Illuminate\Database\Seeder;

class OperationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
        }

        $tuitionAsignacionVoluntariaId = Tuition::where('title', 'Asignacion Voluntaria')->value('tuition_id');
        $tuitionRBMN = Tuition::where('title', 'RBMN')->value('tuition_id');

    
        $operations = [
            ['IMPONIBLEEIMPUTABLE', 1,
                "$tuitionAsignacionVoluntariaId + $tuitionRBMN", NULL, 0, 0, 0, '111111111111'],
            ['TOTALAPAGAR', 1, 'TOTALHABERES - TOTALDESCUENTOS', NULL, 0, 0, 0, '111111111111'],
            ['TOTALAPAGAR', 2, 'TOTALHABERES - TOTALDESCUENTOS', NULL, 0, 0, 0, '111111111111'],
            ['AFP', 1, 'COTIZACIONAFP / 100 * RENTAIMPONIBLE', NULL, 0, 0, 0, '111111111111'],
            ['SALUD', 1, 'COTIZACIONISAPRE * RENTAIMPONIBLE / 100', NULL, 0, 0, 0, '111111111111'],
            ['AFP', 2, 'COTIZACIONAFP / 100 * RENTAIMPONIBLE', NULL, 0, 0, 0, '111111111111'],
            ['SALUD', 2, 'COTIZACIONISAPRE * RENTAIMPONIBLE / 100', NULL, 0, 0, 0, '111111111111'],
            ['DESCUENTOSLEGALES', 1, 'AFP + SALUD + SEGUROCESANTIA', NULL, 0, 0, 0, '111111111111'],
            ['DESCUENTOSLEGALES', 2, 'AFP + SALUD + SEGUROCESANTIA', NULL, 0, 0, 0, '111111111111'],
            ['RENTAIMPONIBLESD', 1, "RTMN + IMPONIBLEYNOIMPUTABLE", NULL, 0, 0, 0, '111111111111'],
            ['RENTAIMPONIBLESD', 2, "SUELDOBASE + $tuitionAsignacionVoluntariaId", NULL, 0, 0, 0, '111111111111'],
            ['DESCUENTOSVOLUNTARIOS', 1, "ADICIONALSALUD + APV + AFPOTRO + ISAPREOTRO", NULL, 0, 0, 0, '111111111111'],
            ['DESCUENTOSVOLUNTARIOS', 2, "ADICIONALSALUD + APV + AFPOTRO + ISAPREOTRO", NULL, 0, 0, 0, '111111111111'],
            ['TOPE30HORAS', 1, 'CARGAHORARIA', NULL, 0, 30, 30, '111111111111'],
            ['TOPE30HORAS', 2, 'CARGAHORARIA', NULL, 0, 30, 30, '111111111111'],
            ['SUMACARGAS', 1, 'CARGAHORARIA', NULL, 0, 0, 0, '111111111111'],
            ['SUMACARGAS', 2, 'CARGAHORARIA', NULL, 0, 0, 0, '111111111111'],
            ['REMUNERACIONTRIBUTABLE', 1, 'RENTAIMPONIBLE - DESCUENTOSLEGALES', 'UF', 0, 60, 60, '111111111111'],
            ['REMUNERACIONTRIBUTABLE', 2, 'RENTAIMPONIBLE - DESCUENTOSLEGALES', 'UF', 0, 60, 60, '111111111111'],
            ['IMPUESTORENTA', 1, 'IMPUESTOUNICO - REBAJAIMPUESTO', NULL, 0, 0, 0, '111111111111'],
            ['IMPUESTORENTA', 2, 'IMPUESTOUNICO - REBAJAIMPUESTO', NULL, 0, 0, 0, '111111111111'],
            ['IMPUESTOUNICO', 1,"IMPUESTOTRAMO2 / IMPUESTOTRAMO2 * FACTORIMPTRAMO2 M+ IMPUESTOTRAMO3 / IMPUESTOTRAMO3 * FACTORIMPTRAMO3 M+ IMPUESTOTRAMO4 / IMPUESTOTRAMO4 * FACTORIMPTRAMO4 M+ IMPUESTOTRAMO5 / IMPUESTOTRAMO5 * FACTORIMPTRAMO5 M+ IMPUESTOTRAMO6 / IMPUESTOTRAMO6 * FACTORIMPTRAMO6 M+ IMPUESTOTRAMO7 / IMPUESTOTRAMO7 * FACTORIMPTRAMO7 M+ IMPUESTOTRAMO8 / IMPUESTOTRAMO8 * FACTORIMPTRAMO8 M+ MR * REMUNERACIONTRIBUTABLE",
                NULL, 0, 0, 0, '111111111111'],
            ['IMPUESTOUNICO', 2, 'IMPUESTOTRAMO2 / IMPUESTOTRAMO2 * FACTORIMPTRAMO2 M+ IMPUESTOTRAMO3 / IMPUESTOTRAMO3 * FACTORIMPTRAMO3 M+ IMPUESTOTRAMO4 / IMPUESTOTRAMO4 * FACTORIMPTRAMO4 M+ IMPUESTOTRAMO5 / IMPUESTOTRAMO5 * FACTORIMPTRAMO5 M+ IMPUESTOTRAMO6 / IMPUESTOTRAMO6 * FACTORIMPTRAMO6 M+ IMPUESTOTRAMO7 / IMPUESTOTRAMO7 * FACTORIMPTRAMO7 M+ IMPUESTOTRAMO8 / IMPUESTOTRAMO8 * FACTORIMPTRAMO8 M+ MR * REMUNERACIONTRIBUTABLE', NULL, 0, 0, 0, '111111111111'],
            ['FILTROCONTRATOFIJO', 1, '1 - ADHIEREASEGURO * TIPOCONTRATO', NULL, 2, 2, 0, '111111111111'],
            ['FILTROCONTRATOFIJO', 2, '1 - ADHIEREASEGURO * TIPOCONTRATO', NULL, 2, 2, 0, '111111111111'],
            ['FILTROCONTRATOINDEF', 1, '1 - ADHIEREASEGURO * TIPOCONTRATO', NULL, 1, 1, 0, '111111111111'],
            ['FILTROCONTRATOINDEF', 2, '1 - ADHIEREASEGURO * TIPOCONTRATO', NULL, 1, 1, 0, '111111111111'],
            ['REBAJAIMPUESTO', 1, 'IMPUESTOTRAMO2  / IMPUESTOTRAMO2 * FACTORREBAJAIMPTRAMO2 M+ IMPUESTOTRAMO3  / IMPUESTOTRAMO3 * FACTORREBAJAIMPTRAMO3 M+ IMPUESTOTRAMO4  / IMPUESTOTRAMO4 * FACTORREBAJAIMPTRAMO4 M+ IMPUESTOTRAMO5  / IMPUESTOTRAMO5 * FACTORREBAJAIMPTRAMO5 M+ IMPUESTOTRAMO6  / IMPUESTOTRAMO6 * FACTORREBAJAIMPTRAMO6 M+ IMPUESTOTRAMO7  / IMPUESTOTRAMO7 * FACTORREBAJAIMPTRAMO7 M+ IMPUESTOTRAMO8  / IMPUESTOTRAMO8 * FACTORREBAJAIMPTRAMO8 M+ MR ', NULL, 0, 0, 0, '111111111111'],
            ['REBAJAIMPUESTO', 2,
                "IMPUESTOTRAMO2 / IMPUESTOTRAMO2 * FACTORREBAJAIMPTRAMO2 M+ IMPUESTOTRAMO3 / IMPUESTOTRAMO3 * FACTORREBAJAIMPTRAMO3 M+ IMPUESTOTRAMO4 / IMPUESTOTRAMO4 * FACTORREBAJAIMPTRAMO4 M+ IMPUESTOTRAMO5 / IMPUESTOTRAMO5 * FACTORREBAJAIMPTRAMO5 M+ IMPUESTOTRAMO6 / IMPUESTOTRAMO6 * FACTORREBAJAIMPTRAMO6 M+ IMPUESTOTRAMO7 / IMPUESTOTRAMO7 * FACTORREBAJAIMPTRAMO7 M+ IMPUESTOTRAMO8 / IMPUESTOTRAMO8 * FACTORREBAJAIMPTRAMO8 M+ MR",
                NULL, 0, 0, 0, '111111111111'],
            ['SEGUROCESANTIA', 1, 'FILTROCONTRATOINDEF / FILTROCONTRATOINDEF * 0.006 * RENTAIMPONIBLE M+ FILTROCONTRATOFIJO / FILTROCONTRATOFIJO * 0.03 * RENTAIMPONIBLE M+ MR', NULL, 0, 0, 0, '111111111111'],
            ['SEGUROCESANTIA', 2,
                "FILTROCONTRATOINDEF / FILTROCONTRATOINDEF * 0.006 * RENTAIMPONIBLE M+ FILTROCONTRATOFIJO / FILTROCONTRATOFIJO * 0.03 * RENTAIMPONIBLE M+ MR",
                NULL, 0, 0, 0, '111111111111'],
            ['ASIGNACIONFAMILIAR', 1,
                "FILTROASIGFAMT1 / FILTROASIGFAMT1 * ASIGCAR.FAMTRAMO1 M+ FILTROASIGFAMT2 / FILTROASIGFAMT2 * ASIGCAR.FAMTRAMO2 M+ FILTROASIGFAMT3 / FILTROASIGFAMT3 * ASIGCAR.FAMTRAMO3 M+ MR * CARGASFAMILIARES",
                NULL, 0, 0, 0, '111111111111'],
            ['ASIGNACIONFAMILIAR', 2, 'FILTROASIGFAMT1 / FILTROASIGFAMT1 * ASIGCAR.FAMTRAMO1 M+ FILTROASIGFAMT2 / FILTROASIGFAMT2 * ASIGCAR.FAMTRAMO2 M+ FILTROASIGFAMT3 / FILTROASIGFAMT3 * ASIGCAR.FAMTRAMO3 M+ MR * CARGASFAMILIARES', NULL, 0, 0, 0, '111111111111'],
            ['LICENCIA', 1, 'RENTAIMPONIBLESD / CARGAHORARIA / 4 * HORASLICENCIA', NULL, 0, 0, 0, '111111111111'],
            ['LICENCIA', 2, 'RENTAIMPONIBLESD / 30 * DIASLICENCIA', NULL, 0, 0, 0, '111111111111'],
            ['ADICIONALSALUD', 1, 'COTIZACIONPACTADA - SALUD', NULL, 0, 0, 0, '111111111111'],
            ['ADICIONALSALUD', 2, 'COTIZACIONPACTADA - SALUD', NULL, 0, 0, 0, '111111111111'],
            ['INASISTENCIA', 1, 'RENTAIMPONIBLESD / CARGAHORARIA / 4 * HORASINASISTENCIA', NULL, 0, 0, 0, '111111111111'],
            ['INASISTENCIA', 2, 'RENTAIMPONIBLESD / 30 * 7 / CARGAHORARIA * HORASINASISTENCIA', NULL, 0, 0, 0, '111111111111'],
            ['IMPONIBLEYNOIMPUTABLE', 1, "HORASPERFECCIONAMIENTO", NULL, 0, 0, 0, '111111111111'],
            ['TOTALNOIMPONIBLE', 1, 'ASIGNACIONFAMILIAR + EXCEDENTEBONOSAELEY19410Y19933', NULL, 0, 0, 0, '111111111111'],
            ['TOTALNOIMPONIBLE', 2, 'ASIGNACIONFAMILIAR', NULL, 0, 0, 0, '111111111111'],
            ['TOTALHABERES', 1, 'RENTAIMPONIBLE + TOTALNOIMPONIBLE', NULL, 0, 0, 0, '111111111111'],
            ['TOTALHABERES', 2, 'RENTAIMPONIBLE + TOTALNOIMPONIBLE', NULL, 0, 0, 0, '111111111111'],
            ['RTMN', 1, 'IMPONIBLEEIMPUTABLE + PLANILLACOMPLEMENTARIA', NULL, 0, 0, 0, '111111111111'],
            ['IMD', 1, 'VALORIMD * CARGAHORARIA', NULL, 0, 0, 0, '111111111111'],
            ['RENTAIMPONIBLE', 1, 'RENTAIMPONIBLESD', NULL, 0, 0, 0, '111111111111'],
            ['RENTAIMPONIBLE', 2, 'RENTAIMPONIBLESD', NULL, 0, 0, 0, '111111111111'],
            ['SUELDOBASE', 2, 'SUELDOBASEB * FACTORASIST', NULL, 0, 0, 0, '111111111111'],
            ['TOTALDESCUENTOS', 1, 'DESCUENTOSLEGALES + DESCUENTOSVOLUNTARIOS + IMPUESTORENTA', NULL, 0, 0, 0, '111111111111'],
            ['TOTALDESCUENTOS', 2, 'DESCUENTOSLEGALES + DESCUENTOSVOLUNTARIOS + IMPUESTORENTA', NULL, 0, 0, 0, '111111111111'],
            ['SUMACARGASTODOS', 1, 'CARGAHORARIA', NULL, 0, 0, 0, '111111111111'],
            ['SUMACARGASTODOS', 2, 'CARGAHORARIA', NULL, 0, 0, 0, '111111111111'],
        ];

        // Obtener el primer contador
        $contadorUser = SchoolUser::first();
        // Verificar si se encontró un contador
        if ($contadorUser) {
            // Obtener el primer colegio asociado a este contador
            $schoolId = $contadorUser->school_id;

            // Iterar sobre cada operación y crear la operación
            foreach ($operations as $operation) {
                Operation::create([
                    'tuition_id' => $operation[0], // Asignamos el tuition_id de acuerdo al título encontrado
                    'worker_type' => $operation[1],
                    'operation' => $operation[2],
                    'limit_unit' => $operation[3],
                    'min_limit' => $operation[4],
                    'max_limit' => $operation[5],
                    'max_value' => $operation[6],
                    'application' => $operation[7],
                    'school_id' => $schoolId,
                ]);
            }
        }
    }

    private function generateBonuses($school_id)
    {
        return [

            [
                'title' => 'Asignacion Voluntaria',
                'type' => 3,
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
                'title' => 'RBMN',
                'type' => 1,
                'taxable' => 0,
                'imputable' => 0,
                'is_bonus' => 0,
                'factor' => 100,
                'application' => 'C',
                'months' => $this->generateDynamicMonths([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]), // Ejemplo de meses específicos
                'school_id' => $school_id,
                'amount' => 7824,
            ]
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
