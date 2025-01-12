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
    /**
     * Selected Action of the index
     */
    public function handleAction($action)
    {
        // Determinar qué acción tomar según el parámetro $action
        switch ($action) {
            case 'list':
                return $this->list();
            case 'params':
                return $this->generalParams();
            case 'worker':
                return $this->workers();
            default:
                abort(404); // Si la acción no es válida, devuelve un error 404
        }
    }
    /**
     * List of the Bonuses or Discounts
     */
    protected function list()
    {
        $schoolId = auth()->user()->school_id_session;

        $bonuses = Bonus::query()
            ->where('school_id', $schoolId) // Filtrar por school_id
            ->orderBy('created_at', 'DESC')->get();

        return view('bonuses.partials.list', compact('bonuses'));
    }
    /**
     * View General Params of the Bonuses or Discounts
     */
    protected function generalParams()
    {
        $this->authorize('parametersGen', Bonus::class); // Verifica si el usuario tiene el permiso adecuado
        $params = Parameter::where('school_id', auth()->user()->school_id_session)->pluck('value', 'name')->toArray();

        return view('bonuses.partials.params', compact('params'));
    }
  /**
     * Update General Params of the Bonuses or Discounts
     */
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
                ['value' => $request->$name, 'updated_at' => now()]
            );
        }

        return redirect()->back()->with('success', 'Parametros generales actualizados..');
    }
    /**
     * View Workers Relations of the Bonuses or Discounts
     */
    protected function workers($worker_id = "")
    {
        // Obtener el id de la escuela del usuario autenticado
        $schoolId = auth()->user()->school_id_session;
        // Obtener todos los trabajadores de la escuela
        $workers = Worker::getWorkersBySchoolAndType($schoolId);
        // Verificar si se pasó un worker_id en la solicitud
        $selectedWorker = null;
        $bonusData = [];
        // Si se selecciona un trabajador, obtener los bonos asociados
        if ($worker_id) {
            $selectedWorker = Worker::find($worker_id);
            // Verificar si el trabajador existe
            if ($selectedWorker) {
                // Obtener los bonos dependientes de ese trabajador
                $bonuses = Bonus::getBonusesByTypeAndApplication($schoolId, $selectedWorker->worker_type, 'D');
                // Obtener los datos de los bonos asociados
                foreach ($bonuses as $bonus) {
                    $value = Parameter::getParameterValue($bonus->tuition_id, $worker_id, $schoolId);
                    $applicable = Parameter::getParameterValue("APLICA" . $bonus->title, $worker_id, $schoolId);
                    $title = Tuition::getTuitionTitle($bonus->title, $schoolId);
                    // Solo agregar bonos que sean aplicables
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
        // Retornar la vista con los trabajadores, el trabajador seleccionado y los bonos
        return view('bonuses.partials.worker', compact('workers', 'selectedWorker', 'bonusData'));
    }
    /**
     * Update Bonuses or Discounts of the Workers
     */
    public function updateBonusWorker(Request $request)
    {
        $worker_id = $request->input('worker_id');
        $schoolId = auth()->user()->school_id_session;
        // Obtener los datos del worker seleccionado
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
                    Parameter::updateOrInsertParamValue($bonus->tuition_id, $worker_id, $schoolId, "", $value);
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
        return redirect()->route('bonuses.partials.worker', ['worker_id' => $worker_id])
        ->with('success', 'Bonos designados correctamente !!');
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
        // Primero, aplicamos el bono a todos los trabajadores
        Parameter::updateParamsSchool_All("APLICA{$title}", $school, 0);

        if (isset($idWorkers)) {
            for ($i = 0; $i < count($idWorkers); $i++) {
                $idWorker = $idWorkers[$i];
                // Verifica si existe el parámetro
                if (Parameter::exists("APLICA{$title}", $idWorker, $school)) {
                    // Actualiza el parámetro si ya existe
                    Parameter::updateOrInsertParamValue("APLICA{$title}", $idWorker, $school, "", 1);
                } else {
                    // Si no existe, puedes también insertar directamente aquí
                    Parameter::create([
                        'name' => "APLICA{$title}",
                        'worker_id' => $idWorker,
                        'school   _id' => $school,
                        'value' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        // Redirigir a la vista de trabajadores
        return redirect()->route('bonuses.workers', $bonus->id)->with('success', 'Trabajadores agregados correctamente.');
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
