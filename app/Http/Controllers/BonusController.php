<?php

namespace App\Http\Controllers;

use App\Http\Requests\BonusFormRequest;
use App\Models\Bonus;
use App\Models\Operation;
use App\Models\Parameter;
use App\Models\Tuition;
use App\Models\Worker;
use Illuminate\Http\Request;

class BonusController extends Controller {

    /**
     * Create the controller instance.
     */
    public function __construct() {
        $this->authorizeResource(Bonus::class, 'bonus');
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        return view('bonuses.index');
    }

    /**
     * Selected Action of the index
     */
    public function handleAction($action) {
        // Determine which action to take based on the $action parameter
        switch ($action) {
            case 'list':
                return $this->listBonuses();
            case 'params':
                return $this->generalParams();
            case 'worker':
                return $this->workers();
            default:
                abort(404); // If the action is invalid, return a 404 error
        }
    }

    /**
     * List of the Bonuses or Discounts
     */
    protected function listBonuses() {
        $schoolId = auth()->user()->school_id_session;

        $bonuses = Bonus::query()
                        ->where('school_id', $schoolId) // Filter by school_id
                        ->orderBy('created_at', 'DESC')->get();

        return view('bonuses.partials.list', compact('bonuses'));
    }

    /**
     * View General Params of the Bonuses or Discounts
     */
    protected function generalParams() {
        $this->authorize('parametersGen', Bonus::class); // Check if the user has the proper permission
        $params = Parameter::where('school_id', auth()->user()->school_id_session)->pluck('value', 'name')->toArray();

        return view('bonuses.partials.params', compact('params'));
    }

    /**
     * Update General Params of the Bonuses or Discounts
     */
    public function updateParams(Request $request) {
        $request->validate([
            'CIERREMES' => 'required',
            '3346b24227961c97d192499c235e46bb' => 'required',
            'VALORIMD' => 'required',
        ]);

        $schoolId = auth()->user()->school_id_session;
        $paramsToUpdate = ['CIERREMES', '3346b24227961c97d192499c235e46bb', 'VALORIMD'];

        foreach ($paramsToUpdate as $name) {
            Parameter::updateOrCreate(
                    ['name' => $name, 'school_id' => $schoolId], ['value' => $request->$name, 'updated_at' => now()]
            );
        }

        return redirect()->back()->with('success', 'Parametros generales actualizados..');
    }

    /**
     * View Workers Relations of the Bonuses or Discounts
     */
    protected function workers($worker_id = "") {
        // Get the school ID of the authenticated user
        $schoolId = auth()->user()->school_id_session;
        // Get all workers from the school
        $workers = Worker::getWorkersBySchoolAndType($schoolId);
        // Check if a worker_id was passed in the request
        $selectedWorker = null;
        $bonusData = [];
        // If a worker is selected, get the associated bonuses
        if ($worker_id) {
            $selectedWorker = Worker::find($worker_id);
            // Check if the worker exists
            if ($selectedWorker) {
                // Get the bonuses dependent on this worker
                $bonuses = Bonus::getBonusesByTypeAndApplication($schoolId, $selectedWorker->worker_type, 'D');
                // Get the associated bonus data
                foreach ($bonuses as $bonus) {
                    $value = Parameter::getParameterValue($bonus->tuition_id, $worker_id, $schoolId);
                    $applicable = Parameter::getParameterValue("APLICA" . $bonus->title, $worker_id, $schoolId);
                    $title = Tuition::getTuitionTitle($bonus->title, $schoolId);
                    // Only add bonuses that are applicable
                    if ($applicable == 1) {
                        $bonusData[] = [
                            'id' => $bonus->tuition_id,
                            'title' => $title,
                            'value' => $value,
                            'aplicable' => $applicable,
                        ];
                    }
                }
            }
        }
        // Return the view with workers, the selected worker, and bonuses
        return view('bonuses.partials.worker', compact('workers', 'selectedWorker', 'bonusData'));
    }

    /**
     * Update Bonuses or Discounts of the Workers
     */
    public function updateBonusWorker(Request $request) {
        $worker_id = $request->input('worker_id');
        $schoolId = auth()->user()->school_id_session;
        // Get the selected worker's data
        $worker = Worker::find($worker_id);
        // Get the bonuses associated with the worker
        $bonuses = Bonus::getBonusesByTypeAndApplication($schoolId, $worker->worker_type, 'D');
        // Iterate over the bonuses and update or insert the value
        foreach ($bonuses as $bonus) {
            $bonusField = "ID$bonus->tuition_id";
            // Check if the bonus value was sent in the request
            if ($request->has($bonusField)) {
                $value = $request->input($bonusField);
                // Validate if the bonus already exists for this worker
                if (Parameter::exists($bonus->tuition_id, $worker_id, $schoolId)) {
                    // If the parameter already exists, update the value
                    Parameter::updateOrInsertParamValue($bonus->tuition_id, $worker_id, $schoolId, "", $value);
                } else {
                    // If the parameter doesn't exist, insert the new value
                    Parameter::create([
                        'name' => $bonus->tuition_id,
                        'worker_id' => $worker_id,
                        'school_id' => $schoolId,
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        // Redirect with success message
        return redirect()->route('bonuses.partials.worker', ['worker_id' => $worker_id])
                        ->with('success', 'Bonos designados correctamente !!');
    }

    /*
      Bonus : add-workers
     */

    public function showWorkers(Bonus $bonus) {
        // Get all workers from the school
        $workers = Worker::getWorkersBySchoolAndType($bonus->school_id, $bonus->type);
        // Get the workers who already have the bonus applied
        $appliedWorkers = [];
        foreach ($workers as $worker) {
            $value = Parameter::getParameterValue("APLICA{$bonus->title}", $worker->id, $bonus->school_id);
            if ($value == 1) {
                $appliedWorkers[] = $worker->id; // Store only the applied worker IDs
            }
        }
        // Filter the non-linked workers
        $nonAppliedWorkers = $workers->whereNotIn('id', $appliedWorkers);

        return view('bonuses.partials.workers', compact('bonus', 'workers', 'nonAppliedWorkers', 'appliedWorkers'));
    }

    public function updateWorkers(Request $request, Bonus $bonus) {
        $idWorkers = $request->input('workers');
        $title = $bonus->title;
        $school = $bonus->school_id;
        // First, apply the bonus to all workers
        Parameter::updateParamsSchool_All("APLICA{$title}", $school, 0);

        if (isset($idWorkers)) {
            for ($i = 0; $i < count($idWorkers); $i++) {
                $idWorker = $idWorkers[$i];
                // Check if the parameter exists
                if (Parameter::exists("APLICA{$title}", $idWorker, $school)) {
                    // Update the parameter if it already exists
                    Parameter::updateOrInsertParamValue("APLICA{$title}", $idWorker, $school, "", 1);
                } else {
                    // If it doesn't exist, you can insert it directly here
                    Parameter::create([
                        'name' => "APLICA{$title}",
                        'worker_id' => $idWorker,
                        'school_id' => $school,
                        'value' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        // Redirect to the workers' view
        return redirect()->route('bonuses.workers', $bonus->id)->with('success', 'Trabajadores agregados correctamente.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        $bonus = new Bonus();

        $applicationOptions = Bonus::APPLICATION_OPTIONS;

        $workerOptions = Operation::getWorkerTypes();

        $allChecked = str_repeat('1', 12); // '111111111111' to mark all months

        return view('bonuses.create', compact('bonus', 'applicationOptions', 'workerOptions', 'allChecked'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BonusFormRequest $request) {
        $result = Bonus::processCreateBonuses($request->validated());

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']); // Redirect with error message
        }

        return redirect()->route('bonuses.partials.list')->with('success', $result['message']); // Success message
    }

    /**
     * Display the specified resource.
     */
    public function show(Bonus $bonus) {
        $applicationOptions = Bonus::APPLICATION_OPTIONS;
        $workerOptions = Operation::getWorkerTypes();
        $type = $bonus->type;
        $schoolId = auth()->user()->school_id_session;
        // Get the applicable months
        if ($type == 3) {
            $mesesapl = Operation::getMounthOperations($bonus->title, 1, $schoolId);
        } else {
            $mesesapl = Operation::getMounthOperations($bonus->title, $type, $schoolId);
        }

        // Determine if each month should be checked using mesesapl
        $allChecked = str_repeat('0', 12); // Initialize as all months unchecked
        if ($mesesapl) {
            $allChecked = $mesesapl; // Use mesesapl to mark the months
        } else if (isset($bonus->school)) {
            $application = $bonus->school->operations->where('tuition_id', $bonus->tuition_id)->first();
            if ($application) {
                $allChecked = $application->application ?? str_repeat('0', 12); // Use the school's application
            }
        }
        return view('bonuses.show', compact('bonus', 'applicationOptions', 'workerOptions', 'allChecked'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bonus $bonus) {
        $applicationOptions = Bonus::APPLICATION_OPTIONS;
        $workerOptions = Operation::getWorkerTypes();

        $type = $bonus->type;
        $schoolId = auth()->user()->school_id_session;
        // Get the applicable months
        if ($type == 3) {
            $mesesapl = Operation::getMounthOperations($bonus->title, 1, $schoolId);
        } else {
            $mesesapl = Operation::getMounthOperations($bonus->title, $type, $schoolId);
        }

        // Determine if each month should be checked using mesesapl
        $allChecked = str_repeat('0', 12); // Initialize as all months unchecked
        if ($mesesapl) {
            $allChecked = $mesesapl; // Use mesesapl to mark the months
        } else if (isset($bonus->school)) {
            $application = $bonus->school->operations->where('tuition_id', $bonus->tuition_id)->first();
            if ($application) {
                $allChecked = $application->application ?? str_repeat('0', 12); // Use the school's application
            }
        }

        return view('bonuses.edit', compact('bonus', 'applicationOptions', 'workerOptions', 'mesesapl', 'allChecked'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BonusFormRequest $request, Bonus $bonus) {
        $data = $request->validated();

        $result = Bonus::processUpdateBonuses($data, $bonus->id);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']); // Redirect with error message
        }

        return redirect()->route('bonuses.partials.list')->with('success', $result['message']); // Success message
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bonus $bonus) {
        $result = Bonus::deleteProcessBonus($bonus);
        //dd($bonus);
        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']); // Redirect with error message
        }

        return redirect()->route('bonuses.partials.list')->with('success', $result['message']); // Success message
    }

}
