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
        $this->authorizeResource(Worker::class, 'worker'); // Authorize resource actions for Worker model
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the school ID of the authenticated user
        $schoolId = auth()->user()->school_id_session;
        // Retrieve workers for the current school with their contracts, ordered by ID and paginated
        $workers = Worker::query()->with('contract')
            ->where('school_id', $schoolId) // Filter by school_id
            ->orderBy('id', 'DESC')
            ->paginate(5); // Pagination with 5 workers per page
                       // Return the view with the workers list
        return view('workers.index', compact('workers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Instantiate a new Worker object
        $worker = new Worker();
        // Get possible values for worker types, function types, contract types, and marital status
        $workerTypes         = Worker::getWorkerTypes();
        $functionWorkerTypes = Worker::getFunctionWorkerTypes();
        $contractTypes       = Contract::getContractTypes();
        $maritalStatus       = Worker::getMaritalStatusTypes();
        // Get all workers from the same school as the authenticated user
        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get();

        return view('workers.create', compact('worker', 'workerTypes', 'functionWorkerTypes', 'contractTypes', 'maritalStatus', 'workers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkerFormRequest $request)
    {
        // Create or update the worker with validated data
        $worker = Worker::createOrUpdateWorker($request->validated());
        // Create or update the worker's contract
        Contract::createOrUpdateContract($worker->id, $request);
        // Create the hourly load array and update the worker
        $worker->createHourlyLoadArray($request);
        // Insert parameters associated with the worker
        Parameter::insertParameters($worker->id, $request, $request->input('school_id'));
        // Redirect to workers list after storing
        return redirect()->route('workers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Worker $worker)
    {
        // Get all workers from the same school
        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get();
        // Return the worker's detail view
        return view('workers.show', compact('worker', 'workers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Worker $worker)
    {
        // Get various types of worker information for editing
        $workerTypes         = Worker::getWorkerTypes();
        $functionWorkerTypes = Worker::getFunctionWorkerTypes();
        $contractTypes       = Contract::getContractTypes();
        $maritalStatus       = Worker::getMaritalStatusTypes();
        // Get all workers from the same school
        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get();
        // Retrieve the contract and associated parameters for the worker
        $contract   = Contract::getContract($worker->id);
        $parameters = Parameter::where('worker_id', $worker->id)->get()->keyBy('name');

        return view('workers.edit', compact('worker', 'workerTypes', 'functionWorkerTypes', 'contractTypes', 'maritalStatus', 'contract', 'parameters', 'workers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkerFormRequest $request, Worker $worker)
    {
        DB::transaction(function () use ($request, $worker) {
            // Update the worker
            $worker = Worker::createOrUpdateWorker($request->validated(), $worker);
            // Update the worker's contract
            Contract::createOrUpdateContract($worker->id, $request);
            // Create the hourly load array and update the worker
            $worker->createHourlyLoadArray($request);
            // Update the worker's parameters
            Parameter::insertParameters($worker->id, $request, $request->input('school_id'));
        });
        // Redirect to the worker's detail page after updating
        return redirect()->route('workers.show', $worker)->with('success', 'Trabajador Actualizado correctamente.');
    }

    /**
     * View to create a contract for the worker.
     */
    public function createContract(Worker $worker)
    {
        $formData = $worker->prepareContractFormData();

        return view('contracts.create', [
            'worker'          => $worker,
            'formData'        => $formData,
            'durationOptions' => Contract::DURATION_OPTIONS,
            'scheduleOptions' => Contract::SCHEDULE_OPTIONS,
            'levelsOptions'   => Contract::LEVELS_OPTIONS,
        ]);
    }

    /**
     * Store a newly created contract for the worker.
     */
    public function storeContract(StoreContractRequest $request, Worker $worker)
    {
        // Retrieve the existing contract for the worker
        $contract = Contract::getContract($worker->id);
        // Group contract details into an array
        $details = [
            'city'               => $request->city,
            'levels'             => $request->levels,
            'duration'           => $request->duration,
            'total_remuneration' => $request->total_remuneration,
            'remuneration_gloss' => $request->remuneration_gloss,
            'origin_city'        => $request->origin_city,
            'schedule'           => $request->schedule,
            'teaching_hours'     => $request->teaching_hours,
            'curricular_hours'   => $request->curricular_hours,
        ];
        // Update or create the contract with the details
        $contract->details = json_encode($details);
        $contract->save();

        return redirect()->route('workers.index'); // Redirect to the workers list after storing contract
    }

    /**
     * Print the contract for the worker.
     */
    public function printContract(Worker $worker)
    {
        // Retrieve the contract details for the worker
        $contract = $worker->contract;
        // Get the annexes and add them to the contract details
        $annexes = $contract ? $contract->annexes : [];
        // Return the view with contract details, including annexes
        return view('contracts.print', [
            'worker'          => $worker,
            'contractDetails' => json_decode($contract->details, true), // Decode the JSON details to an array
            'annexes'         => $annexes,                              // Include annexes in the contract details
        ]);
    }

    /**
     * View the settlements of the workers.
     */
    public function settlements()
    {
        $schoolId = auth()->user()->school_id_session;

        // Obtener solo los trabajadores cuyo campo 'settlement_date' no es nulo
        $workers = Worker::where('school_id', $schoolId)
            ->whereNotNull('settlement_date') // Filtrar por 'settlement_date' no nulo
            ->orderBy('id', 'DESC')
            ->paginate(5);

        return view('workers.settlements.index', compact('workers'));
    }

    /**
     * View the settlement form for a specific worker.
     */
    public function settle(Worker $worker)
    {
        return view('workers.settlements.settle', compact('worker')); // Return view to settle the worker
    }

    /**
     * Update the settlement date for the worker.
     */
    public function updateSettlementDate(Request $request, Worker $worker)
    {
        // Validate the settlement date
        $request->validate([
            'settlement_date' => 'required|date',
        ]);
        // Set the settlement date
        $worker->settlement_date = $request->input('settlement_date');
        // Save the worker's settlement date
        $worker->save();
        // Redirect with success message
        return redirect()->route('workers.index')->with('success', 'Fecha de finiquito actualizada correctamente.');
    }

    /**
     * Remove the settlement date from a worker.
     */
    public function removeSettlementDate(Worker $worker)
    {
        // Set the settlement_date to null
        $worker->settlement_date = null;
        $worker->save(); // Save the changes

        return redirect()->route('workers.settlements')->with('success', 'Fecha de finiquito eliminada correctamente.');
    }

    /**
     * Show annexes associated with the worker's contract.
     */
    public function showAnnexes(Worker $worker)
    {
        // Get the contract of the worker and any associated annexes
        $contract = Contract::getContract($worker->id);
        // If the contract exists and has attachments in JSON format, we convert them into a collection
        $annexes = collect();
        if ($contract && ! empty($contract->annexes)) {
            // Asumimos que 'annexes' es un array o JSON válido
            $annexes = collect(json_decode($contract->annexes, true));
        }
        // Return the view with the worker and annexes
        return view('contracts.annexes.index', compact('worker', 'annexes'));
    }

    /**
     * Show the form to create an annex in the worker's contract.
     */
    public function createAnnex(Worker $worker)
    {
        // Return the view to create a new annex
        return view('contracts.annexes.create', compact('worker'));
    }

    /**
     * Store a newly created annex in the worker's contract.
     */
    public function storeAnnex(Request $request, Worker $worker)
    {
        // Validate annex name and description
        $request->validate([
            'annex_name'        => 'required|string|max:255',
            'annex_description' => 'required|string',
        ]);
        // Retrieve the contract for the worker
        $contract = Contract::getContract($worker->id);
        // Get current annexes (if any)
        $annexes = $contract ? $contract->annexes : [];
        // Create a new annex with a unique ID and created_at timestamp
        $newAnnex = [
            'id'                => uniqid(), // Generate a unique ID for the annex
            'annex_name'        => $request->input('annex_name'),
            'annex_description' => $request->input('annex_description'),
            'created_at'        => now()->toDateTimeString(), // Add created_at timestamp
        ];

        // Add the new annex to the existing annexes
        $annexes[] = $newAnnex;

        // Update the contract with the new annexes array
        $contract->update([
            'annexes' => $annexes,
        ]);

        // Redirect back to the annexes page with a success message
        return redirect()->route('contracts.showAnnexes', $worker)->with('success', 'Anexo Registrado Exitosamente !!');
    }

    /**
     * Edit an annex in the worker's contract.
     */
    public function editAnnex(Worker $worker, $annex)
    {
        // Get the contract and the specific annex to be edited
        $contract  = $worker->contract;
        $annexData = collect(json_decode($contract->annexes, true))->firstWhere('id', $annex);
        // Return the edit view for the annex
        return view('contracts.annexes.edit', compact('worker', 'annexData'));
    }

    /**
     * Update an annex in the worker's contract.
     */
    public function updateAnnex(Request $request, Worker $worker, $annex)
    {
        // Validate annex name and description
        $request->validate([
            'annex_name'        => 'required|string|max:255',
            'annex_description' => 'required|string',
        ]);

        $contract = $worker->contract;
        $annexes  = $contract->annexes ?? [];
        // Update the annex data
        foreach ($annexes as &$existingAnnex) {
            if ($existingAnnex['id'] == $annex) {
                $existingAnnex['annex_name']        = $request->input('annex_name');
                $existingAnnex['annex_description'] = $request->input('annex_description');
                $existingAnnex['created_at']        = now()->toDateTimeString(); // Update the created_at timestamp
                break;
            }
        }
        // Save the updated annexes array to the contract
        $contract->update(['annexes' => $annexes]);

        return redirect()->route('contracts.showAnnexes', $worker)->with('success', 'Anexo actualizado con éxito');
    }

    /**
     * Delete an annex from the worker's contract.
     */
    public function deleteAnnex(Worker $worker, $annex)
    {
        // Retrieve the contract and filter out the annex to be deleted
        $contract = $worker->contract;
        $annexes  = collect($contract->annexes)->filter(function ($annexData) use ($annex) {
            return $annexData['id'] !== $annex;
        })->values();
        // Update the contract with the remaining annexes
        $contract->update(['annexes' => $annexes]);
        // Redirect back to the annexes page with a success message
        return redirect()->route('contracts.showAnnexes', $worker)->with('success', 'Anexo Eliminado Exitosamente !!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Worker $worker)
    {
        Parameter::where('worker_id', $worker->id)->delete();
        // Delete the worker
        $worker->delete();
        // Redirect to workers list after deletion
        return redirect()->route('workers.index')->with('success', 'Trabajador Eliminado Exitosamente !!');
    }

}
