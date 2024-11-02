<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\WorkerFormRequest;
use App\Models\Contract;
use App\Models\Parameter;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

class WorkerController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Worker::class, 'worker');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schoolId = auth()->user()->school_id_session;

        $workers = Worker::query()
            ->where('school_id', $schoolId) // Filtrar por school_id
            ->orderBy('id', 'DESC')
            ->paginate(5); // Paginación

        return view('workers.index', compact('workers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $worker = new Worker();
        $workerTypes = Worker::getWorkerTypes();
        $functionWorkerTypes = Worker::getFunctionWorkerTypes();
        $contractTypes = Contract::getContractTypes();
        $maritalStatus = Worker::getMaritalStatusTypes();
        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get(); // Obtiene trabajadores de la misma escuela
        return view('workers.create', compact('worker', 'workerTypes', 'functionWorkerTypes', 'contractTypes', 'maritalStatus', 'workers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkerFormRequest $request)
    {
        // Crear o actualizar el trabajador
        $worker = Worker::createOrUpdateWorker($request->validated());
        // Actualizar el contrato
        Contract::createOrUpdateContract($worker->id, $request);
        // Crear el arreglo de carga horaria
        $hourlyLoadArray = Worker::createHourlyLoadArray($request);
        $worker->updateHourlyLoad($hourlyLoadArray);
        // Insertar parámetros
        Parameter::insertParameters($worker->id, $request, $request->input('school_id'));

        return redirect()->route('workers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Worker $worker)
    {
        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get(); // Obtiene trabajadores de la misma escuela

        return view('workers.show', compact('worker', 'workers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Worker $worker)
    {
        $workerTypes = Worker::getWorkerTypes();
        $functionWorkerTypes = Worker::getFunctionWorkerTypes();
        $contractTypes = Contract::getContractTypes();
        $maritalStatus = Worker::getMaritalStatusTypes();

        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get(); // Obtiene trabajadores de la misma escuela

        // Obtener el contrato y parámetros asociados
        $contract = Contract::getContract($worker->id);
        $parameters = Parameter::where('worker_id', $worker->id)->get()->keyBy('name');

        return view('workers.edit', compact('worker', 'workerTypes', 'functionWorkerTypes', 'contractTypes', 'maritalStatus', 'contract', 'parameters', 'workers'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(WorkerFormRequest $request, Worker $worker)
    {
        DB::transaction(function () use ($request, $worker) {
            // Actualizar el trabajador
            $worker = Worker::createOrUpdateWorker($request->validated(), $worker);
            // Actualizar el contrato
            Contract::createOrUpdateContract($worker->id, $request);
            // Crear el arreglo de carga horaria
            $hourlyLoadArray = Worker::createHourlyLoadArray($request);
            $worker->updateHourlyLoad($hourlyLoadArray);
            // Actualizar los parámetros
            Parameter::insertParameters($worker->id, $request, $request->input('school_id'));
        });

        return redirect()->route('workers.show', $worker)->with('success', 'Trabajador actualizado correctamente.');
    }

    public function createContract(Worker $worker)
    {
        return view('contracts.create', compact('worker'));
    }

    public function storeContract(StoreContractRequest $request, Worker $worker)
    {
        // Busca el contrato existente
        $contract = $worker->contract;

        // Agrupar datos en un array
        $details = [
            'city' => $request->city,
            'levels' => $request->levels,
            'duration' => $request->duration,
            'total_remuneration' => $request->total_remuneration,
            'remuneration_gloss' => $request->remuneration_gloss,
            'origin_city' => $request->origin_city,
            'schedule' => $request->schedule,
        ];

        // Actualizar el contrato existente
        $contract->update(['details' => json_encode($details)]);

        return redirect()->route('workers.index', $worker);
    }

    public function printContract(Worker $worker)
    {
        // Obtener los detalles del contrato
        $contract = $worker->contract;

        // Pasar los datos a la vista
        return view('contracts.print', [
            'worker' => $worker,
            'contractDetails' => json_decode($contract->details, true), // Convertir a array
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Worker $worker)
    {
        $worker->delete();

        return redirect()->route('workers.index');
    }
}
