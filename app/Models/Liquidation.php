<?php

namespace App\Models;

use App\Helpers\LiquidationHelper;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liquidation extends Model
{
    use HasFactory;

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'worker_id',
        'month',
        'year',
        'values',
        'details', // Añadimos 'details' para que se pueda asignar masivamente
        'glosa', // Añadimos glosa al $fillable si vas a asignarlo masivamente
    ];

    public static function storeLiquidation($request, $workerId)
    {
        // Inicia la liquidación para el trabajador, mes y año proporcionados
        $month = $request["month"];
        $year = $request['year'];

        // Primero eliminamos la liquidación anterior (si existe)
        self::deleteLiquidation($month, $year, $workerId);

        // Creamos la nueva liquidación
        $liquidation = self::create([
            'worker_id' => $workerId,
            'month' => $month,
            'year' => $year,
        ]);

        // Inicializamos un array para almacenar los detalles de la liquidación
        $details = [];
        $clases = Tuition::where('school_id', $request->school_id)->get();

        foreach ($clases as $clase) {
            $idval = 'VALID' . $clase->tuition_id;
            $idtit = 'TITID' . $clase->tuition_id;
            // Si el valor 'VALID' existe en la solicitud, lo usamos
            if ($request->has($idval)) {
                // Agregar los detalles al array
                $details[] = [
                    'tuition_id' => $clase->tuition_id, // Asignar el ID de la liquidación
                    'title' => $request->input($idtit), // Decodificar el título
                    'value' => $request->input($idval), // Obtener el valor ingresado
                ];
            }
        }

        // Si hay detalles para guardar, los asignamos al objeto liquidación
        if (!empty($details)) {
            // Asignar los detalles en el campo 'details'
            $liquidation->setDetailsAttribute($details);
            // Guardar la liquidación con los detalles
            $liquidation->save();
        }

        // Generar la glosa de la liquidación
        $glosa = self::generateGlosa($request, $workerId, $liquidation->id, $request->school_id);
        $liquidation->glosa = $glosa;
        $liquidation->save();

        return $liquidation;
    }

    // Método para generar la glosa
    private static function generateGlosa($request, $workerId, $liquidationId, $school_id)
    {
        // Obtener los datos de la plantilla y el trabajador
        $worker = Worker::find($workerId);
        $plantilla = Template::getTemplate($request->school_id, $worker->worker_type);

        // Llamamos a getHeaderGlosa para obtener los datos de la cabecera
        $headerData = self::getHeaderGlosa($liquidationId, $workerId, $school_id);

        // Obtener detalles de la glosa
        $details = '';
        foreach ($plantilla as $row) {
            $codigo = trim($row->code);
            $clase = $row->tuition_id;
            $ignoreZero = $row->ignore_zero;
            $parentheses = $row->parentheses;
            $clastit = self::getDetailByTuitionId($liquidationId, $clase, 'title');
            $clasval = self::getDetailByTuitionId($liquidationId, $clase, 'value');
            $clasval3 = $clasval;

            if (($ignoreZero == 1) && ($clasval == 0)) {
                continue; // Saltar filas que tienen valor cero y se deben ignorar
            }

            switch ($codigo) {
                case "N":
                    $details .= "<tr><td>" . ($clastit ? $clastit : "") . "</td></tr>";
                    break;
                case "NV":
                    $details .= "<tr><td>" . ($clastit ? $clastit : "") . "</td><td class='text-right'>$</td><td class='text-right'>" . $clasval . "</td></tr>";
                    break;
                case "NVV":
                    if ($clastit) {
                        $clasval3 = ($parentheses == 1) ? "($clasval)" : $clasval;
                        $details .= "<tr><td>" . $clastit . "</td><td class='text-right'>$</td><td class='text-right'>" . $clasval . "</td><td class='text-right'>$</td><td class='text-right'>" . $clasval3 . "</td></tr>";
                    }
                    break;
                case "N V":
                    if ($clastit) {
                        $clasval3 = ($parentheses == 1) ? "($clasval)" : $clasval;
                        $details .= "<tr><td>" . $clastit . "</td><td></td><td></td><td class='text-right'>$</td><td class='text-right'>" . $clasval3 . "</td></tr>";
                    }
                    break;
                case "_L_":
                case "LLL":
                    $details .= "<tr><td colspan='5' class='center'><hr/></td></tr>";
                    break;
                case "TEX":
                    $details .= "<tr><td colspan='5' class='center'><u>" . $clase . "</u></td></tr>";
                    break;
                case "":
                    $details .= "<tr><td>&nbsp;</td></tr>";
                    break;
            }
            $details .= "</tr>";
        }
        $details .= "</tbody></table>";

        // Llamar a la vista y pasar los datos
        $glosa = view('liquidations.liquidationData', [
            'headerData' => $headerData,
            'details' => $details,
        ])->render();

        // Escapar comillas simples para el almacenamiento en campo binary
        $glosa = str_replace("'", "\'", $glosa);

        return $glosa;
    }
// Generar los datos de la cabecera
    public static function getHeaderGlosa($liquidationId, $workerId, $school_id)
    {
        $daysworkers = self::getDetailByTuitionId($liquidationId, "DIASTRABAJADOS", 'value');
        $dataGlosa = LiquidationHelper::getHeaderLiquidation($workerId, $school_id, now()->month);

        $worker = $dataGlosa['worker'];
        $school = $dataGlosa['school'];
        $monthTxt = $dataGlosa['monthTxt'];
        $workload = $dataGlosa['workload'];
        $year = now()->year;

        // Organizar los datos para pasarlos a la vista
        $headerData = [
            'school_name' => $school->name,
            'school_rut' => $school->rut,
            'school_rbd' => $school->rbd,
            'month_txt' => $monthTxt,
            'year' => $year,
            'worker_name' => $worker->name,
            'worker_last_name' => $worker->last_name,
            'worker_rut' => $worker->rut,
            'workload' => $workload,
            'worker_function' => $worker->getFunctionWorkerTypes()[$worker->function_worker],
            'days_worked' => $daysworkers,
        ];

        return $headerData;
    }

    public static function getDetailByTuitionId($liquidationId, $tuitionId, $field)
    {
        // Obtener la liquidación por su ID
        $liquidation = self::find($liquidationId);
        // Verificamos si la liquidación existe
        if (!$liquidation) {
            return 0; // Si no existe la liquidación, retornamos null
        }
        // Accedemos a los detalles de la liquidación
        $details = $liquidation->details;
        // Buscamos el tuition en los detalles de la liquidación
        foreach ($details as $detail) {
            if (isset($detail['tuition_id']) && $detail['tuition_id'] == $tuitionId) {
                // Devolvemos el valor del campo que se ha solicitado (title o value)
                return $detail[$field];
            }
        }
        // Si no encontramos el tuition_id, retornamos null
        return 0;
    }
    
    public static function getDistinctYears()
    {
        // Using 'distinct' and 'pluck' to get unique years
        return self::distinct('year')->pluck('year');
    }

    // Acceder a los detalles como un array
    public function getDetailsAttribute($value)
    {
        return json_decode($value, true); // Decodifica el JSON almacenado en 'details' a un array
    }

    // Modificar los detalles antes de guardarlos en el modelo
    public function setDetailsAttribute($value)
    {
        // Si ya existen detalles, los decodificamos y los fusionamos con los nuevos
        if (isset($this->attributes['details'])) {
            $existingDetails = json_decode($this->attributes['details'], true);
        } else {
            $existingDetails = [];
        }
        // Fusionamos los detalles existentes con los nuevos
        $mergedDetails = array_merge($existingDetails, $value);
        // Guardamos los detalles combinados en formato JSON
        $this->attributes['details'] = json_encode($mergedDetails);
    }

    private static function deleteLiquidation($month, $year, $workerId)
    {
        $liquidation = self::where('month', $month)
            ->where('year', $year)
            ->where('worker_id', $workerId)
            ->first(); // Buscar el primer registro que coincida
        // Si no existe un registro, retornamos 1
        if (!$liquidation) {
            return 1;
        }
        // Si existe un registro, procedemos a eliminarlo
        $liquidation->delete();
    }
    /**
     * Verifica si existe una liquidación para un trabajador, mes y año específicos.
     *
     * @param int $month
     * @param int $year
     * @param int $workerId
     * @return bool
     */
    public static function exists($month, $year, $workerId)
    {
        // Usamos Eloquent para realizar la consulta
        $liquidacion = self::where('month', $month)
            ->where('year', $year)
            ->where('worker_id', $workerId)
            ->first(); // Devuelve el primer resultado o null si no existe
        // Si la liquidación existe, retornamos true, si no, false
        return $liquidacion ? true : false;
    }

    public static function getLiquidationsByType($month, $year, $type, $schoolId)
    {
        $result = self::where('month', $month)
            ->where('year', $year)
            ->whereHas('worker', function ($query) use ($type, $schoolId) {
                $query->where('worker_type', $type)
                    ->where('school_id', $schoolId)
                    ->whereNull('settlement_date'); // Equivalent of fec_finiquito
            })
            ->get(); // Assuming id_liquidacion is the 'id' field in liquidations table

        return $result;
    }

    // Relación con el modelo Worker
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
