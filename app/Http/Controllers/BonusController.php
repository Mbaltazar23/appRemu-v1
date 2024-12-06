<?php

namespace App\Http\Controllers;

use App\Http\Requests\BonusFormRequest;
use App\Models\Bonus;
use App\Models\Operation;
use App\Models\Parameter;
use App\Models\Tuition;
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
            ->orderBy('id', 'ASC')
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
                ['value' => $request->$name, 'created_at' => now(),
                    'updated_at' => now()]
            );
        }

        return redirect()->back();
    }

        /*
    Bonus : add-workers
     */
    public function showWorkers(Bonus $bonus)
    {
        // Obtener todos los trabajadores de la escuela
        $workers = Worker::getWorkersBySchoolAndType($bonus->school_id, $bonus->type);

        // Obtener los trabajadores que ya tienen el bono aplicado
        $appliedWorkers = [];
        foreach ($workers as $worker) {
            $value = Parameter::getParameterValue("APLICA{$bonus->title}", $worker->id, $bonus->school_id);
            if ($value == 1) {
                $appliedWorkers[] = $worker->id; // Guardar solo los IDs de los trabajadores aplicados
            }
        }

        // Filtrar los trabajadores no vinculados
        $nonAppliedWorkers = $workers->whereNotIn('id', $appliedWorkers);

        return view('bonuses.partials.workers', compact('bonus', 'workers', 'nonAppliedWorkers', 'appliedWorkers'));
    }

    public function updateWorkers(Request $request, Bonus $bonus)
    {
        $idWorkers = $request->input('workers');
        $title = $bonus->title;
        $school = $bonus->school_id;
        //dd($idWorkers, $school, $tuition);
        // Primero, aplicamos el bono a todos los trabajadores
        Parameter::updateParamsSchool_All("APLICA{$title}", $school, 0);

        if (isset($idWorkers)) {
            for ($i = 0; $i < count($idWorkers); $i++) {
                $idWorker = $idWorkers[$i];

                // Verifica si existe el parámetro
                if (Parameter::exists("APLICA{$title}", $idWorker, $school)) {
                    // Actualiza el parámetro si ya existe
                    Parameter::updateOrInsertParamValue("APLICA{$title}", $idWorker, 1, $school);
                } else {
                    // Si no existe, puedes también insertar directamente aquí
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

        // Redirigir a la vista de trabajadores
        return redirect()->route('bonuses.workers', $bonus->id)->with('message', 'Trabajadores actualizados correctamente.');
    }

    public function workers()
    {
        $schoolId = auth()->user()->school_id_session;
        $workers = Worker::getWorkersBySchoolAndType($schoolId); // example fetching workers
        return view('bonuses.partials.worker', compact('workers'));
    }

    public function fetchWorkerParameters($workerId)
    {
        $schoolId = auth()->user()->school_id_session;
        $worker = Worker::find($workerId);

        if (!$worker) {
            return response()->json(['error' => 'Trabajador no encontrado.'], 404);
        }

        $bonuses = Bonus::getBonusesByTypeAndApplication($schoolId, $worker->worker_type, 'D');

        $bonusData = [];
        if ($bonuses->count() > 0) {
            foreach ($bonuses as $bonus) {
                $value = Parameter::getParameterValue($bonus->tuition_id, $workerId, $schoolId);
                $aplicable = Parameter::getParameterValue("APLICA" . $bonus->title, $workerId, $schoolId);
                $title = Tuition::getTuitionTitle($bonus->title, $schoolId);

                if ($aplicable == 1) {
                    $bonusData[] = [
                        'id' => $bonus->tuition_id,
                        'title' => $title,
                        'value' => $value,
                        'aplicable' => $aplicable,
                    ];
                }

            }
        }
        return response()->json([
            'name' => $worker->name . ' ' . $worker->last_name,
            'bonuses' => $bonusData,
            'type' => $worker->getWorkerTypes()[$worker->worker_type],
        ]);
    }

    public function updateBonusWorker(Request $request)
    {
        $worker_id = $request->input('worker_id');
        $schoolId = auth()->user()->school_id_session;

        $worker = Worker::find($worker_id);
        // Obtener los bonos asociados al trabajador
        $bonuses = Bonus::getBonusesByTypeAndApplication($schoolId, $worker->worker_type, 'D');

        // Iterar sobre los bonos y actualizar o insertar el valor
        foreach ($bonuses as $bonus) {
            $bonusField = "ID$bonus->tuition_id";
            // Verificar si el valor del bono fue enviado en la solicitud
            if ($request->has($bonusField)) {
                $value = $request->input($bonusField);
                // Validar si el bono ya existe para este trabajador
                if (Parameter::exists($bonus->tuition_id, $worker_id, $schoolId)) {
                    // Si el parametro ya existe, actualizar el valor
                    Parameter::updateOrInsertParamValue($bonus->tuition_id, $worker_id, $schoolId, $value);
                } else {
                    // Si el parametro no existe, insertar el nuevo valor
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

        // Redirigir con mensaje de éxito
        return redirect()->route('bonuses.partials.worker')
            ->with('success', 'Bonos actualizados correctamente.')
            ->with('worker_id', $worker_id);
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
            $mesesapl = Operation::getMounthOperations($bonus->title, 1, $schoolId);
        } else {
            $mesesapl = Operation::getMounthOperations($bonus->title, $type, $schoolId);
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
            $mesesapl = Operation::getMounthOperations($bonus->title, 1, $schoolId);
        } else {
            $mesesapl = Operation::getMounthOperations($bonus->title, $type, $schoolId);
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
