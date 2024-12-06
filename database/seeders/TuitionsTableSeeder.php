<?php

namespace Database\Seeders;

use App\Models\Tuition;
use App\Models\User;
use Illuminate\Database\Seeder;

class TuitionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            ['TOTALAPAGAR', 'Total a Pagar', 'O', '', 1, 0],
            ['RENTAIMPONIBLE', 'Renta Imponible', 'O', '', 1, 0],
            ['FACTORRBMNBASICA', 'Factor RMBN Basica', 'P', '', 0, 1],
            ['CARGAHORARIA', 'Carga horaria', 'P', '', 0, 0],
            ['DESCUENTOSLEGALES', 'Descuentos previsionales', 'O', '', 1, 0],
            ['AFP', 'AFP', 'O', '', 1, 0],
            ['SALUD', 'Salud', 'O', '', 1, 0],
            ['COTIZACIONPACTADA', 'Cotización pactada', 'P', '', 0, 0],
            ['AFPTRABAJADOR', 'AFP Trabajador', 'P', '', 0, 0],
            ['IMPUESTOUNICO', 'Impuesto Único', 'O', '', 1, 0],
            ['REBAJAIMPUESTO', 'Rebaja Impuesto', 'O', '', 1, 0],
            ['REMUNERACIONTRIBUTABLE', 'Remuneración tributable', 'O', '', 1, 0],
            ['IMPUESTORENTA', 'Impuesto Renta', 'O', '', 1, 0],
            ['CARGASFAMILIARES', 'Cargas Familiares Trabajador', 'P', '', 0, 0],
            ['TIPOCONTRATO', 'Tipo contrato', 'P', '', 0, 0],
            ['YEARINICIOSERVICIO', 'YEARINICIOSERVICIO', 'P', '', 0, 0],
            ['APV', 'APV', 'P', '', 1, 0],
            ['SEGUROCESANTIA', 'Seguro cesantia', 'O', '', 1, 0],
            ['HORASPERFECCIONAMIENTO', 'Horas perfeccionamiento', 'P', '', 1, 1],
            ['SUMACARGAS', 'Suma Cargas', 'S', '', 0, 0],
            ['YEARACTUAL', 'YEARACTUAL', 'P', '', 0, 0],
            ['SUELDOBASE', 'Sueldo Base', 'O', '', 1, 0],
            ['EXCEDENTEBONOSAELEY19410Y19933', 'Excedente Bono SAE ley 19410/19933', 'O', '', 1, 0],
            ['UTM', 'Valor UTM', 'P', '', 0, 0],
            ['UF', 'Valor UF', 'P', '', 0, 0],
            ['DESCUENTOSVOLUNTARIOS', 'Descuentos voluntarios', 'O', '', 1, 0],
            ['TOPE30HORAS', 'TOPE30HORAS', 'O', '', 0, 0],
            ['FILTROCONTRATOFIJO', 'FILTROCONTRATOFIJO', 'O', '', 0, 0],
            ['FILTROCONTRATOINDEF', 'FILTROCONTRATOINDEF', 'O', '', 0, 0],
            ['ASIGNACIONFAMILIAR', 'Asignacion familiar', 'O', '', 1, 0],
            ['LICENCIA', 'Licencia médica', 'O', '', 1, 0],
            ['COSTOHORALICENCIADOCENTE', 'Costo por hora de licencia para docentes', 'P', '', 0, 1, 8],
            ['COSTODIALICENCIANODOCENTE', 'Costo por día de licencia para no docentes', 'P', '', 0, 1, 8],
            ['DIASLICENCIA', 'Días licencia no docente', 'P', '', 0, 0],
            ['COTIZACIONISAPRE', 'Cotización en isapre', 'P', '', 0, 0],
            ['COTIZACIONAFP', 'Cotización AFP del trabajador', 'P', '', 0, 0],
            ['CIERREMES', 'Día del cierre de mes', 'P', '', 0, 1],
            ['UFAFP', 'Valor UF aplicable a afps', 'P', '', 0, 0],
            ['UFINP', 'Valor UF aplicable al INP', 'P', '', 0, 0],
            ['ADICIONALSALUD', 'Adicional de salud', 'O', '', 1, 1],
            ['AFPOTRO', 'Otros descuentos previsionales', 'P', '', 1, 1],
            ['ISAPREOTRO', 'Otros descuentos en salud', 'P', '', 1, 1],
            ['HORASLICENCIA', 'Horas licencia docente', 'P', '', 0, 0],
            ['COSTOHORAINASISTENCIADOCENTE', 'Costo por hora de inasistencia docente', 'P', '', 0, 0],
            ['COSTOHORAINASISTENCIANODOCENTE', 'Costo por hora de inasistencia no docente', 'P', '', 0, 0],
            ['HORASINASISTENCIA', 'Horas de inasistencia', 'P', '', 0, 0],
            ['INASISTENCIA', 'Inasistencia', 'O', '', 1, 0],
            ['PLANILLACOMPLEMENTARIA', 'Planilla complementaria', 'O', '', 1, 1],
            ['RTMN', 'R.T.M.N', 'O', '', 1, 0],
            ['IMPONIBLEYNOIMPUTABLE', 'Imponible y No imputable a la R.T.M.N.', 'O', '', 1, 0],
            ['TOTALNOIMPONIBLE', 'Total no imponible', 'O', '', 1, 0],
            ['TOTALHABERES', 'Total Haberes', 'O', '', 1, 0],
            ['TOTALDESCUENTOSPREVISIONALES', 'Total Dctos Previsionales', 'O', '', 1, 0],
            ['ADHIEREASEGURO', 'Si adhiere o no a seguro de cesantia', 'P', '', 0, 0],
            ['DIASTRABAJADOS', 'Días trabajados', 'P', '', 1, 1],
            ['IMPONIBLEEIMPUTABLE', 'Imponible e imputable a la RTMN', 'O', '', 1, 1],
            ['VALORIMD', 'Valor IMD', 'P', '', 0, 0],
            ['IMD', 'IMD', 'O', '', 0, 0],
            ['SUMACARGASTODOS', 'Suma carga todos los trabajadores', 'S', '', 0, 0],
            ['RENTAIMPONIBLESD', 'Renta imponible sin descuentos', 'O', '', 0, 0],
            ['FACTORASIST', 'Factor de asistencia y licencia medica', 'P', '', 0, 0],
            ['SUELDOBASEB', 'Sueldo base bruto', 'P', '', 0, 0],
            ['TOTALDESCUENTOS', 'Total Descuentos', 'O', '', 1, 0],
        ];
// Obtener el primer contador
        $contadorUser = User::where('role', User::CONTADOR)->first();

// Verificar si se encontró un contador
        if ($contadorUser) {
            // Obtener el primer colegio asociado a este contador
            $schoolId = $contadorUser->schools->first()->id;
            // Insertar los registros en la tabla `tuitions`
            foreach ($data as $tuition) {
                Tuition::create([
                    'tuition_id' => $tuition[0],
                    'title' => $tuition[1],
                    'type' => $tuition[2],
                    'description' => $tuition[3],
                    'in_liquidation' => $tuition[4],
                    'editable' => $tuition[5],
                    'school_id' => $schoolId,
                ]);
            }
        }
    }
}
