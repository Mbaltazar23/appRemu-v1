<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    use HasFactory;

    /**
     * Obtiene las opciones para el selector de 'item'.
     *
     * @return array
     */
    public static function getItemOptions()
    {
        return [
            'item_ds' => 'Directivos superiores',
            'item_ad' => 'Administrativos',
            'item_do' => 'Docentes',
        ];
    }

    /**
     * Obtiene las opciones para el selector de 'periodo'.
     *
     * @return array
     */
    public static function getPeriodOptions()
    {
        return [
            'Enero' => 'Enero',
            'Febrero' => 'Febrero',
            'Marzo' => 'Marzo',
            'Abril' => 'Abril',
            'Mayo' => 'Mayo',
            'Junio' => 'Junio',
            'Julio' => 'Julio',
            'Agosto' => 'Agosto',
            'Septiembre' => 'Septiembre',
            'Octubre' => 'Octubre',
            'Noviembre' => 'Noviembre',
            'Diciembre' => 'Diciembre',
            '1erSemestre' => '1er Semestre',
            '2doSemestre' => '2do Semestre',
            'Anual' => 'Año completo',
        ];
    }

    /**
     * Obtiene las sumas de los totales de liquidaciones para una escuela, basado en un 'item' y 'periodo'.
     *
     * @param int $school_id
     * @param string $item
     * @param string $periodo
     * @return array|bool
     */
    public static function getLiquidationSumsTotalCosts($school_id, $item, $periodo)
    {
        // Obtener las condiciones de filtrado para el item y periodo
        $conditions = self::generateFilterConditions($item, $periodo);

        // Si el método de validación devuelve un error, lo gestionamos aquí
        if (isset($conditions['error'])) {
            return $conditions['error']; // Error si no es un item o periodo válido
        }

        $strfuncion = $conditions['strfuncion'];
        $strmeses = $conditions['strmeses'];

        // Definir las categorías que queremos sumar
        $categories = [
            'RENTAIMPONIBLE', 'DESCUENTOSLEGALES', 'IMPUESTORENTA', 'DESCUENTOSVOLUNTARIOS',
            'TOTALAPAGAR', 'AFP', 'SALUD', 'SEGUROCESANTIA', 'LICENCIA', 'INASISTENCIA',
        ];

        // Obtener los trabajadores de la escuela, filtrando primero por school_id
        $workers = Worker::where('school_id', $school_id) // Filtrar por school_id
            ->where(function ($query) use ($strfuncion) {
                $query->whereRaw($strfuncion); // Filtrar según la función del trabajador
            })
            ->get(); // Traer todos los trabajadores que coincidan con ambas condiciones

// Verificar si la colección está vacía
if ($workers->count() === 0) { // Usamos count() para obtener la cantidad de elementos en la colección
    return [
                'workers' => [], // Retornamos un array vacío si no hay trabajadores
            ];
        }

        // Recorrer los trabajadores y sumar las liquidaciones correspondientes
        foreach ($workers as $worker) {
            // Inicializamos un array para acumular los totales por trabajador
            $workerTotals = array_fill_keys($categories, 0);

            foreach ($categories as $category) {
                // Llamamos a la función que obtiene la suma de la liquidación por trabajador y mes
                $workerTotals[$category] = self::getSumLiquidationsByWorkerAndMonth(
                    $worker->id, $category, $strfuncion, $strmeses
                );
            }

            // También podemos almacenar los datos de los trabajadores si se requieren
            $worker->totals = $workerTotals;
        }

        // Retornamos los totales y los trabajadores con sus respectivos totales
        return [
            'workers' => $workers,
        ];
    }

    /**
     * Obtiene la suma de los valores de liquidación para un trabajador específico,
     * un mes, y una clase (tuition_id dentro de details), además de filtrar por la función del trabajador.
     *
     * @param int $workerId
     * @param string $tuitionId (ID de la tuition, que está dentro de details como tuition_id)
     * @param string $strfunction (Función del trabajador, ejemplo: "Docente de aula")
     * @param string $strmonth (Mes a filtrar, por ejemplo: "month=1" para enero)
     * @return float
     */
    public static function getSumLiquidationsByWorkerAndMonth($workerId, $tuitionId, $strfunction, $strmonth)
    {
        // Obtenemos las liquidaciones del trabajador filtradas por la función y mes
        $liquidations = Liquidation::where('worker_id', $workerId)
            ->whereRaw($strmonth)
            ->whereHas('worker', function ($query) use ($strfunction) {
                $query->whereRaw($strfunction); // Filtro por función del trabajador
            })
            ->get();

        // Si no tiene liquidaciones, retornamos 0
        if ($liquidations->isEmpty()) {
            return 0;
        }
        // Variable para acumular la suma de los valores
        $totalSum = 0;
        // Iteramos sobre las liquidaciones para sumar los valores dentro de los detalles
        foreach ($liquidations as $liquidation) {
            // Accedemos a los detalles de la liquidación (es un JSON)
            $details = $liquidation->details;
            // Recorremos los detalles de la liquidación
            foreach ($details as $detail) {
                // Verificamos si el 'tuition_id' coincide con el valor buscado
                if (isset($detail['tuition_id']) && $detail['tuition_id'] == $tuitionId) {
                    // Si existe 'value', lo sumamos después de limpiar las comas (en caso de formato numérico)
                    if (isset($detail['value'])) {
                        $totalSum += (float) str_replace(',', '', $detail['value']);
                    }
                }
            }
        }
        // Retornamos la suma total (si no hay valores, retornamos 0)
        return $totalSum;
    }

    /**
     * Genera las condiciones de filtro basadas en el 'item' y 'periodo'.
     *
     * @param string $item
     * @param string $periodo
     * @return array
     */
    public static function generateFilterConditions($item, $periodo)
    {
        // Mapeo de las funciones para cada tipo de item
        $funciones = [
            'item_ds' => "function_worker=5 OR function_worker=6 OR function_worker=7", // Directivos
            'item_ad' => "function_worker=3 OR function_worker=8 OR function_worker=9", // Administrativos
            'item_do' => "function_worker=1", // Docentes
        ];
        // Mapeo de los meses
        $meses = [
            'Enero' => "month=1", 'Febrero' => "month=2", 'Marzo' => "month=3", 'Abril' => "month=4",
            'Mayo' => "month=5", 'Junio' => "month=6", 'Julio' => "month=7", 'Agosto' => "month=8",
            'Septiembre' => "month=9", 'Octubre' => "month=10", 'Noviembre' => "month=11", 'Diciembre' => "month=12",
            '1erSemestre' => "month<7", '2doSemestre' => "month>6", 'Anual' => "month>0",
        ];
        // Validación de 'item' y 'periodo' en base al mapeo
        if (!isset($funciones[$item]) || !isset($meses[$periodo])) {
            return [
                'error' => 'Datos del item o periodo incorrectos.',
            ];
        }

        return [
            'strfuncion' => $funciones[$item],
            'strmeses' => $meses[$periodo],
            'tititem' => ucfirst(str_replace('item_', '', $item)), // Convierte item_ds -> Directivos, item_ad -> Administrativos
            'titperiodo' => $periodo,
        ];
    }
}
