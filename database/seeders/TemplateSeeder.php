<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\Tuition;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer contador
        $contadorUser = User::where('role', User::CONTADOR)->first();
        // Verificar si se encontró un contador
        if ($contadorUser) {
            // Obtener el primer colegio asociado a este contador
            $school_id = $contadorUser->schools->first()->id; // Obtener el primer colegio de los colegios asociados al contador

            $tuitionRBMN = Tuition::where('title', 'RBMN')->where('school_id', $school_id)->value('tuition_id');
            $tuitionUMP = Tuition::where('title', 'UMP')->where('school_id', $school_id)->value('tuition_id');
            $tuitionLey19410 = Tuition::where('title', 'Ley 19410')->where('school_id', $school_id)->value('tuition_id');
            $tuitionLey19933 = Tuition::where('title', 'Ley 19933')->where('school_id', $school_id)->value('tuition_id');
            $tuitionLey19464 = Tuition::where('title', 'Ley 19464')->where('school_id', $school_id)->value('tuition_id');
            $tuitionPerfeccionamiento = Tuition::where('title', 'Perfeccionamiento')->where('school_id', $school_id)->value('tuition_id');
            $tuitionAsigVolun = Tuition::where('title', 'Asignacion Voluntaria')->where('school_id', $school_id)->value('tuition_id');
            $tuitionDesempnio= Tuition::where('title', 'Desempeño dificil')->where('school_id', $school_id)->value('tuition_id');
            $tuitionColegioPro = Tuition::where('title', 'Colegio de profesores')->where('school_id', $school_id)->value('tuition_id');
            $tuitionFundacionLP = Tuition::where('title', 'Fundacion Lopez Perez')->where('school_id', $school_id)->value('tuition_id');

            $tuitionPresSocialAnd = Tuition::where('title', 'Prestamo Social Caja los Andes')->where('school_id', $school_id)->value('tuition_id');

            $tuitionPresSocialHer = Tuition::where('title', 'Prestamo Social Caja los heroes')->where('school_id', $school_id)->value('tuition_id');

            $tuitionCuentaAhorroAnd = Tuition::where('title', 'Cuenta Ahorro Caja los Andes')->where('school_id', $school_id)->value('tuition_id');

            $tuitionOtros = Tuition::where('title', 'Otros')->where('school_id', $school_id)->value('tuition_id');

            $templatesType1 = [
                ['TEX', 'Imponible e imputable a la RTMN', 0, 0],
                ['NV', $tuitionRBMN, 0, 0],
                ['NV', $tuitionUMP, 0, 0],
                ['NV', $tuitionLey19410, 0, 0],
                ['NV', $tuitionLey19933, 1, 0],
                ['NV', $tuitionPerfeccionamiento, 0, 0],
                ['NV', $tuitionAsigVolun, 0, 0],
                ['NV', $tuitionDesempnio, 0, 0],
                ['NV', 'INASISTENCIA', 1, 0],
                ['NV', 'LICENCIA', 1, 0],
                ['_L_', '', 0, 0],
                ['NV', 'RENTAIMPONIBLE', 0, 0],
                ['NV', 'ASIGNACIONFAMILIAR', 0, 0],
                [null, '', 0, 0],
                ['NVV', 'TOTALHABERES', 0, 0],
                ['NV', 'AFP', 0, 0],
                ['NV', 'SALUD', 0, 0],
                ['NV', 'ADICIONALSALUD', 1, 0],
                ['NV', 'SEGUROCESANTIA', 0, 0],
                ['NV', 'DESCUENTOSLEGALES', 0, 1],
                ['TEX', 'Calculo de Imp. a la Renta', 0, 0],
                ['NV', 'REMUNERACIONTRIBUTABLE', 0, 0],
                ['NV', 'IMPUESTORENTA', 0, 1],
                ['NV', 'APV', 1, 0],
                ['NV', $tuitionColegioPro, 1, 0],
                ['NV', $tuitionFundacionLP, 1, 0],
                ['NV', $tuitionPresSocialAnd, 1, 0],
                ['NV', $tuitionPresSocialHer, 1, 0],
                ['NV', $tuitionOtros, 1, 0],
                [null, '', 0, 0],
                ['_L_', '', 0, 0],
                ['NV', 'DESCUENTOSVOLUNTARIOS', 0, 1],
                ['NVV', 'TOTALDESCUENTOS', 0, 1],
                ['__L', '', 0, 0],
                ['N V', 'TOTALAPAGAR', 0, 0],
            ];

            // Insertar plantillas de tipo 1 (Docente)
        foreach ($templatesType1 as $index => $template) {
            Template::create([
                'school_id' => $school_id,
                'type' => Worker::WORKER_TYPE_TEACHER,  // Docente
                'position' => $index + 1, // Genera el valor de position a partir del índice del bucle
                'code' => $template[0],
                'tuition_id' => $template[1],
                'ignore_zero' => $template[2],
                'parentheses' => $template[3],
            ]);
        }

            $templatesType2 = [
                ['NV', 'SUELDOBASE', 0, 0],
                ['NV', $tuitionAsigVolun, 1, 0],
                ['NV', $tuitionDesempnio, 0, 0],
                ['NV', $tuitionLey19464, 0, 0],
                ['NV', 'INASISTENCIA', 1, 0],
                ['_L_', '', 0, 0],
                ['NV', 'RENTAIMPONIBLE', 0, 0],
                ['NV', 'ASIGNACIONFAMILIAR', 1, 0],
                ['NV', 'TOTALNOIMPONIBLE', 1, 0],
                ['_L_', '', 0, 0],
                ['NVV', 'TOTALHABERES', 0, 0],
                ['NV', 'AFP', 0, 0],
                ['NV', 'SEGUROCESANTIA', 1, 0],
                ['NV', 'SALUD', 0, 0],
                ['_L_', '', 0, 0],
                ['NV', 'DESCUENTOSLEGALES', 0, 1],
                [null, '', 0, 0],
                ['TEX', 'Calculo de Imp. a la Renta', 0, 0],
                ['NV', 'RENTAIMPONIBLE', 0, 0],
                ['NV', 'DESCUENTOSLEGALES', 0, 0],
                [null, '', 0, 0],
                ['_L_', '', 0, 0],
                ['NV', 'REMUNERACIONTRIBUTABLE', 0, 0],
                ['NV', 'IMPUESTOUNICO', 0, 0],
                ['NV', 'REBAJAIMPUESTO', 0, 0],
                ['_L_', '', 0, 0],
                ['NV', 'IMPUESTORENTA', 0, 1],
                ['NV', 'APV', 1, 0],
                ['NV', 'ADICIONALSALUD', 1, 0],
                [null, '', 0, 0],
                ['NV', $tuitionPresSocialAnd, 1, 0],
                ['NV', $tuitionPresSocialHer, 1, 0],
                ['NV', $tuitionCuentaAhorroAnd, 1, 0],
                ['NV', $tuitionFundacionLP, 1, 0],
                ['_L_', '', 0, 0],
                ['NV', 'DESCUENTOSVOLUNTARIOS', 0, 1],
                ['NV', 'TOTALDESCUENTOS', 0, 1],
                ['_L_', '', 0, 0],
                ['NV', 'TOTALAPAGAR', 0, 0],
            ];
            

            foreach ($templatesType2 as $index => $template) {
                Template::create([
                    'school_id' => $school_id,
                    'type' => Worker::WORKER_TYPE_NON_TEACHER,  // Docente
                    'position' => $index + 1, // Genera el valor de position a partir del índice del bucle
                    'code' => $template[0],
                    'tuition_id' => $template[1],
                    'ignore_zero' => $template[2],
                    'parentheses' => $template[3],
                ]);
            }
        }
    }
}
