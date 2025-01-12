<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\WorkerFormRequest;
use App\Models\Contract;
use App\Models\Parameter;
use App\Models\Worker;
use Illuminate\Http\Request;
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

        return redirect()->route('workers.show', $worker)->with('success', 'Trabajador Actualizado correctamente.');
    }
    /**
     * View of Create the contract of the worker
     */
    public function createContract(Worker $worker)
    {
        return view('contracts.create', [
            'worker' => $worker,
            'durationOptions' => Contract::DURATION_OPTIONS,
            'scheduleOptions' => Contract::SCHEDULE_OPTIONS,
            'levelsOptions' => Contract::LEVELS_OPTIONS,
        ]);
    }
    /**
     * Store Create the contract of the worker
     */
    public function storeContract(StoreContractRequest $request, Worker $worker)
    {
        // Busca el contrato existente
        $contract = Contract::getContract($worker->id);
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
        // Actualizar o crear el contrato con los detalles
        $contract->details = json_encode($details);
        $contract->save();

        return redirect()->route('workers.index');
    }
    /**
     * Print of the contract of the worker
     */
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
     * View Settlements of the worker
     */
    public function settlements()
    {
        $schoolId = auth()->user()->school_id_session;
        // Obtener trabajadores con finiquito
        $workers = Worker::where('school_id', $schoolId)
            ->whereNotNull('settlement_date')
            ->orderBy('id', 'DESC')
            ->paginate(5); // Paginación de 5 trabajadores por página
        //dd($workers);
        return view('workers.settlements.index', compact('workers'));
    }
    /**
     * Set Settle of the worker
     */
    public function settle(Worker $worker)
    {
        return view('workers.settlements.settle', compact('worker'));
    }
    /**
     * Update Settle  of the worker
     */
    public function updateSettlementDate(Request $request, Worker $worker)
    {
        $request->validate([
            'settlement_date' => 'required|date',
        ]);
        $worker->settlement_date = $request->input('settlement_date');
        $worker->save();
        
        return redirect()->route('workers.index')->with('success', 'Fecha de finiquito actualizada correctamente.');
    }
    /**
     * Show Annexes of the contract of the worker
     */
    public function showAnnexes(Worker $worker)
    {
        // Obtener el contrato del trabajador y los anexos (si existen)
        $contract = Contract::getContract($worker->id);
        $annexes = $contract ? $contract->annexes : [];
        // Retornar la vista de la ventana emergente con los anexos
        return view('contracts.annexes.index', compact('worker', 'annexes'));
    }
    /**
     * Create Annexes in the contract of the worker
     */
    public function createAnnex(Worker $worker)
    {
        return view('contracts.annexes.create', compact('worker'));
    }

    /**
     * Store Annex in the contract of the worker
     */
    public function storeAnnex(Request $request, Worker $worker)
    {
        $request->validate([
            'annex_name' => 'required|string|max:255',
            'annex_description' => 'required|string',
        ]);
        // Obtener el contrato del trabajador
        $contract = Contract::getContract($worker->id);
        // Obtener los anexos actuales (si existen)
        $annexes = $contract ? $contract->annexes : [];
        // Crear un nuevo anexo con un ID único
        $newAnnex = [
            'id' => uniqid(), // Genera un ID único
            'annex_name' => $request->input('annex_name'),
            'annex_description' => $request->input('annex_description'),
        ];
        // Agregar el nuevo anexo al array de anexos
        $annexes[] = $newAnnex;
        // Guardar los anexos actualizados en el contrato
        $contract->update([
            'annexes' => $annexes,
        ]);
        // Redirigir de nuevo a la ventana emergente con los anexos actualizados
        return redirect()->route('contracts.showAnnexes', $worker)->with('success', 'Anexo Registrado Exitosamente !!');
    }
    /**
     * Edit Annex in the contract of the worker
     */
    public function editAnnex(Worker $worker, $annex)
    {
        $contract = $worker->contract;
        $annexData = collect($contract->annexes)->firstWhere('id', $annex);

        return view('contracts.annexes.edit', compact('worker', 'annexData'));
    }
    /**
     * Update Annex in the contract of the worker
     */
    public function updateAnnex(Request $request, Worker $worker, $annex)
    {
        $request->validate([
            'annex_name' => 'required|string|max:255',
            'annex_description' => 'required|string',
        ]);
        $contract = $worker->contract;
        $annexes = $contract->annexes ?? [];

        foreach ($annexes as &$existingAnnex) {
            if ($existingAnnex['id'] == $annex) {
                $existingAnnex['annex_name'] = $request->input('annex_name');
                $existingAnnex['annex_description'] = $request->input('annex_description');
                break;
            }
        }
        $contract->update(['annexes' => $annexes]);

        return redirect()->route('contracts.showAnnexes', $worker)->with('success', 'Anexo actualizado con éxito');
    }
    /**
     * Delete Annex in the contract of the worker
     */
    public function deleteAnnex(Worker $worker, $annex)
    {
        $contract = $worker->contract;
        $annexes = collect($contract->annexes)->filter(function ($annexData) use ($annex) {
            return $annexData['id'] !== $annex;
        })->values();

        $contract->update(['annexes' => $annexes]);
        // Redirigir de nuevo a la página de anexos con el trabajador
        return redirect()->route('contracts.showAnnexes', $worker)->with('success', 'Anexo Eliminado Exitosamente !!');
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
