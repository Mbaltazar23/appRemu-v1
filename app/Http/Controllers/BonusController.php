<?php

namespace App\Http\Controllers;

use App\Http\Requests\BonusFormRequest;
use App\Models\Bonus;
use App\Models\Operation;
use App\Models\Parameter;
use App\Models\School;
use App\Models\Worker;
use Illuminate\Http\Request;

class BonusController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Bonus::class, 'bonus');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('bonuses.index');
    }

    public function list()
    {
        $schoolId = auth()->user()->school_id_session;

        $bonuses = Bonus::query()
            ->where('school_id', $schoolId) // Filtrar por school_id
            ->orderBy('id', 'DESC')
            ->paginate(5); // Paginación

        return view('bonuses.partials.list', compact('bonuses'));
    }

    public function generalParams()
    {
        $params = Parameter::where('school_id', auth()->user()->school_id_session)->pluck('value', 'name')->toArray();

        return view('bonuses.partials.params', compact('params'));
    }

    public function updateParams(Request $request)
    {
        $request->validate([
            'CIERREMES' => 'required',
            'FACTORRBMNBASICA' => 'required',
            'VALORIMD' => 'required',
        ]);

        $schoolId = auth()->user()->school_id_session;
        $paramsToUpdate = ['CIERREMES', 'FACTORRBMNBASICA', 'VALORIMD'];

        foreach ($paramsToUpdate as $name) {
            Parameter::updateOrCreate(
                ['name' => $name, 'school_id' => $schoolId],
                ['value' => $request->$name]
            );
        }

        return redirect()->back();
    }

    public function worker()
    {
        $workers = Worker::all(); // example fetching workers
        return view('bonuses.partials.worker', compact('workers'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bonus = new Bonus();

        $applicationOptions = Bonus::APPLICATION_OPTIONS;

        $workerOptions = Operation::getWorkerTypes();

        $allChecked = str_repeat('1', 12); // '111111111111' para marcar todos los meses

        return view('bonuses.create', compact('bonus', 'applicationOptions', 'workerOptions', 'allChecked'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BonusFormRequest $request)
    {
        $result = Bonus::processCreateBonuses($request->validated());

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']); // Redirigir con mensaje de error
        }

        return redirect()->route('bonuses.partials.list')->with('success', $result['message']); // Mensaje de éxito
    }

    /**
     * Display the specified resource.
     */
    public function show(Bonus $bonus)
    {
        $applicationOptions = Bonus::APPLICATION_OPTIONS;
        $workerOptions = Operation::getWorkerTypes();
        $type = $bonus->type;
        $schoolId = auth()->user()->school_id_session;
        // Obtener los meses aplicables
        if ($type == 3) {
            $mesesapl = Operation::getMounthOperations($bonus->tuition_id, 1, $schoolId);
        } else {
            $mesesapl = Operation::getMounthOperations($bonus->tuition_id, $type, $schoolId);
        }

// Determinar si se debe marcar cada mes usando mesesapl
        $allChecked = str_repeat('0', 12); // Inicializa como todos los meses no marcados
        if ($mesesapl) {
            $allChecked = $mesesapl; // Usar mesesapl para marcar los meses
        } else if (isset($bonus->school)) {
            $application = $bonus->school->operations->where('tuition_id', $bonus->tuition_id)->first();
            if ($application) {
                $allChecked = $application->application ?? str_repeat('0', 12); // Usar la aplicación de la escuela
            }
        }
        return view('bonuses.show', compact('bonus', 'applicationOptions', 'workerOptions', 'allChecked'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bonus $bonus)
    {
        $applicationOptions = Bonus::APPLICATION_OPTIONS;
        $workerOptions = Operation::getWorkerTypes();

        $type = $bonus->type;
        $schoolId = auth()->user()->school_id_session;
        // Obtener los meses aplicables
        if ($type == 3) {
            $mesesapl = Operation::getMounthOperations($bonus->tuition_id, 1, $schoolId);
        } else {
            $mesesapl = Operation::getMounthOperations($bonus->tuition_id, $type, $schoolId);
        }

        // Determinar si se debe marcar cada mes usando mesesapl
        $allChecked = str_repeat('0', 12); // Inicializa como todos los meses no marcados
        if ($mesesapl) {
            $allChecked = $mesesapl; // Usar mesesapl para marcar los meses
        } else if (isset($bonus->school)) {
            $application = $bonus->school->operations->where('tuition_id', $bonus->tuition_id)->first();
            if ($application) {
                $allChecked = $application->application ?? str_repeat('0', 12); // Usar la aplicación de la escuela
            }
        }

        return view('bonuses.edit', compact('bonus', 'applicationOptions', 'workerOptions', 'mesesapl', 'allChecked'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BonusFormRequest $request, Bonus $bonus)
    {
        $data = $request->validated();

        $result = Bonus::processUpdateBonuses($data, $bonus->id);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']); // Redirigir con mensaje de error
        }

        return redirect()->route('bonuses.partials.list')->with('success', $result['message']); // Mensaje de éxito
    }

    /*
    Bonus : add-workers
     */
    public function showWorkers(Bonus $bonus)
    {
        // Obtener todos los trabajadores de la escuela
        $workers = Worker::where('school_id', $bonus->school_id)->get();

        // Obtener los trabajadores que ya tienen el bono aplicado
        $appliedWorkers = [];
        foreach ($workers as $worker) {
            $value = Parameter::getParameterValue("APLICA{$bonus->tuition_id}", $worker->id, $bonus->school_id);
            if ($value == 1) {
                $appliedWorkers[] = $worker->id; // Guardar solo los IDs de los trabajadores aplicados
            }
        }

        return view('bonuses.partials.workers', compact('bonus', 'workers', 'appliedWorkers'));
    }

    public function updateWorkers(Request $request, Bonus $bonus)
    {
        $idWorkers = $request->input('workers');
        $tuition = $bonus->tuition_id; // O cualquier otra propiedad que defina la clase
        $school = $bonus->school_id; // ID de la escuela

        // Primero, aplicamos el bono a todos los trabajadores
        Parameter::updateParamsSchool_All("APLICA{$tuition}", $school, 0);

        if (isset($idWorkers) && count($idWorkers) > 0) {
            foreach ($idWorkers as $idWorker) {
                if (Parameter::exists("APLICA{$tuition}", $idWorker, $school)) {
                    // Actualiza el parámetro si ya existe
                    Parameter::updateOrInsertParamValue("APLICA{$tuition}", 1);
                } else {
                    // Crea un nuevo parámetro si no existe
                    Parameter::create([
                        'name' => "APLICA{$tuition}",
                        'worker_id' => $idWorker,
                        'school_id' => $school,
                        'value' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Redirigir a la vista de trabajadores
        return redirect()->route('bonuses.workers', $bonus->id)->with('message', 'Trabajadores actualizados correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bonus $bonus)
    {
        $result = Bonus::deleteProcessBonus($bonus);

        //dd($bonus);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']); // Redirigir con mensaje de error
        }

        return redirect()->route('bonuses.partials.list')->with('success', $result['message']); // Mensaje de éxito
    }
}
