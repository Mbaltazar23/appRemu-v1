<?php

namespace App\Http\Controllers;

use App\Helpers\LiquidationHelper;
use App\Helpers\MonthHelper;
use App\Models\Liquidation;
use App\Models\TmpLiquidation;
use App\Models\Worker;
use Illuminate\Http\Request;

class LiquidationController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Liquidation::class, 'liquidation');
    }

    // Vista principal: selecciona el tipo de trabajador
    public function index()
    {
        $workerTypes = Worker::getWorkerTypes(); // Obtener los tipos de trabajadores
        return view('liquidations.index', compact('workerTypes'));
    }

    // Vista para seleccionar un trabajador según el tipo
    public function selectWorkerType($workerType)
    {
        $school_id = auth()->user()->school_id_session;
        $distincYears = Liquidation::getDistinctYears();
        // Obtener los trabajadores por tipo y por escuela
        $workers = Worker::where('worker_type', $workerType)
            ->where('school_id', $school_id)
            ->get();

        return view('liquidations.selectWorker', compact('workers', 'workerType', 'distincYears'));
    }

    public function workerLiquidation($workerId)
    {
        // Eliminar la sesión 'tmp' si existe
        TmpLiquidation::truncate();
        $worker = Worker::findOrFail($workerId);
        $liquidations = Liquidation::where('worker_id', $workerId)->get();

        return view('liquidations.workerLiquidation', compact('worker', 'liquidations'));
    }

    public function create($workerId)
    {
        $school_id = auth()->user()->school_id_session; // Obtener el ID de la escuela del usuario
        $worker = Worker::findOrFail($workerId);

        $warning = "";
        // Verificar si ya existe una liquidación para el trabajador en el mes y año actuales
        if (Liquidation::exists(now()->month, now()->year, $workerId)) {
            // Si existe una liquidación, redirigir de nuevo con el mensaje de advertencia
            $warning = 'La liquidación existente será reemplazada en caso de que desee guardar esta';
        }

        // Validar si el workerType coincide con el trabajador
        $liquidationHelper = new LiquidationHelper();
        $errorMessage = $liquidationHelper->checkAFPandISAPRE($workerId, $school_id);

        // Si hay un error, redirigir de nuevo con el mensaje de error
        if ($errorMessage) {
            return redirect()->back()
                ->withInput() // Esto es opcional, pero mantiene los valores del formulario previo
                ->with('error', $errorMessage)
                ->with('workerId', $workerId); // Pasamos el workerId como parámetro
        }
        // Obtener los datos para la liquidación
        $liquidationHelper->getLiquidationData($workerId, $school_id);
        // Pasar los datos a la vista 'liquidations.create'
        return view('liquidations.create', [
            'worker' => $worker,
            'tmp' => TmpLiquidation::all(), // Pasar los datos de la "tabla temporal" para la liquidación
        ])->with('warning', $warning);
    }

    public function store(Request $request, Worker $worker)
    {
        // Intentar almacenar la liquidación
        $liquidation = Liquidation::storeLiquidation($request, $worker->id);
        // Comprobar si la liquidación fue creada correctamente
        if ($liquidation) {
            // Si se ha creado correctamente, mensaje de éxito
            session()->flash('success', 'La liquidación se ha creado correctamente.');
        } else {
            // Si no se pudo crear la liquidación (por ejemplo, un error de validación)
            session()->flash('error', 'Hubo un problema al crear la liquidación.');
        }
        // Redirigimos de vuelta al formulario 'create', pasando el workerId y la escuela
        return redirect()->route('liquidations.workerLiquidation', ['workerId' => $worker->id]);
    }

    public function getGlosa($id)
    {
        // Buscar la liquidación por su ID
        $liquidation = Liquidation::findOrFail($id);
        // Obtener la glosa (que contiene la tabla HTML generada)
        $glosa = $liquidation->glosa;
        // Devolver la glosa como respuesta HTML
        return response($glosa);
    }

    public function printGlosas(Request $request, $type)
    {
        $school_id = auth()->user()->school_id_session; // Obtener el ID de la escuela del usuario autenticado
        $year = $request->input('year');
        $month = $request->input('month');
        $mountText = MonthHelper::integerToMonth($month);
        // Validación del formulario: si el año no es válido, redirigir
        if ($request->input('year') == 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Debe seleccionar un año para mostrar las liquidaciones.')
                ->with('workerType', $type);
        }
        // Obtener las liquidaciones por tipo, mes, año y escuela
        $liquidations = Liquidation::getLiquidationsByType($month, $year, $type, $school_id);
        // Recuperar las glosas de cada liquidación

        //dd($liquidations);
        $glosas = [];
        foreach ($liquidations as $liquidation) {
            $glosas[] = $liquidation->glosa; // Suponiendo que 'glosa' es el contenido HTML
        }

        // Pasamos las glosas a la vista
        return view('liquidations.printGlosas', compact('glosas', 'mountText', 'year'));
    }

}
