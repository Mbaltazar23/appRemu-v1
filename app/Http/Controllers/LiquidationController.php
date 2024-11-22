<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Models\Liquidation;
use App\Helpers\LiquidationHelper;

class LiquidationController extends Controller
{

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

        // Obtener los trabajadores por tipo y por escuela
        $workers = Worker::where('worker_type', $workerType)
            ->where('school_id', $school_id)
            ->get();

        return view('liquidations.selectWorker', compact('workers', 'workerType'));
    }

    public function workerLiquidation($workerId)
    {
        $worker = Worker::findOrFail($workerId);
        $liquidations = Liquidation::where('worker_id', $workerId)->get();

        return view('liquidations.workerLiquidation', compact('worker', 'liquidations'));
    }

    public function create($workerId)
    {
        $school_id = auth()->user()->school_id_session; // Obtener el ID de la escuela del usuario
        $worker = Worker::findOrFail($workerId);
        // Validar si el workerType coincide con el trabajador
        $liquidationHelper = new LiquidationHelper();
        $errorMessage = $liquidationHelper->checkAFPandISAPRE($workerId, $school_id);
        // Si hay un error, redirigir de nuevo con el mensaje de error
        if ($errorMessage) {
            return redirect()->back()
                ->withInput() // Esto es opcional, pero mantiene los valores del formulario previo.
                ->with('error', $errorMessage)
                ->with('workerId', $workerId); // Pasamos el workerId como parámetro
        }
        // Obtener los datos para la liquidación
        $liquidationHelper->getLiquidationData($workerId, $school_id);
        // Pasar los datos a la vista 'liquidations.create'
        return view('liquidations.create', [
            'worker' => $worker,
            'tmp' => session()->get('tmp'), // Pasar los datos de la "tabla temporal" para la liquidación
        ]);
    }

}
