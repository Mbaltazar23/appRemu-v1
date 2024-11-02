<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsuranceFormRequest;
use App\Models\Insurance;
use App\Models\Parameter;
use App\Models\Worker;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Insurance::class, 'insurance');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Iniciar la consulta
        $query = Insurance::query();

        // Filtrar por tipo si se pasa en la consulta
        if ($request->has('type')) {
            $type = $request->input('type');
            $query->where('type', $type);
        }

        // Obtener los insurances ordenados y paginados
        $insurances = Insurance::where('type', $type)->paginate(5)->appends(['type' => $type]);

        // Capturamos el tipo para pasar a la vista
        $type = $request->input('type');

        // Retornar la vista con los insurances y el tipo
        return view('insurances.index', compact('insurances', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $insurance = new Insurance();

        // Captura el tipo desde la solicitud
        $type = $request->input('type');

        return view('insurances.create', compact('insurance', 'type'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InsuranceFormRequest $request)
    {
        Insurance::create($request->validated());
        $type = $request->input('type');

        return redirect()->route('insurances.index', ['type' => $type]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Insurance $insurance, Request $request)
    {
        // Captura el tipo desde la solicitud
        $type = $request->input('type');

        // Obtiene los tipos de seguros
        $insuranceTypes = Insurance::getInsuranceTypes();

        return view('insurances.show', compact('insurance', 'type', 'insuranceTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Insurance $insurance, Request $request)
    {
        // Captura el tipo desde la solicitud
        $type = $request->input('type');

        return view('insurances.edit', compact('insurance', 'type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InsuranceFormRequest $request, Insurance $insurance)
    {
        $insurance->update($request->validated());

        // Captura el tipo de la solicitud y lo pasa al redirigir
        $type = $request->input('type');

        return redirect()->route('insurances.index', ['type' => $type]);
    }

    /** View Link Worker */
    public function linkWorker(Request $request, $insuranceId)
    {
        $school_id = auth()->user()->school_id_session;
        $insurance = Insurance::findOrFail($insuranceId);
        $type = $insurance->type;

        // Obtener trabajadores del school_id y que no estén vinculados a un seguro del mismo tipo
        $workers = Worker::where('school_id', $school_id)
            ->get();

        return view('insurances.link_worker', compact('insurance', 'workers', 'type'));
    }

    public function getWorkerParameters($workerId, $typeInsurance)
    {
        $worker = Worker::findOrFail($workerId);

        $parameters = $worker->getWorkerParameters($typeInsurance);

        return response()->json($parameters);
    }

    /** Send Link Worker To Insurance */

    public function attachWorker(Request $request, $insuranceId)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
        ]);
    
        $worker = Worker::findOrFail($request->worker_id);
        
        // Vincula el trabajador al seguro
        if ($request->input('type') !== Insurance::ISAPRE) {
            $worker->insurance_AFP = $insuranceId;
        } else {
            $worker->insurance_ISAPRE = $insuranceId;
        }

        $worker->save();
        // Obtiene los parámetros del formulario
        $params = $request->only(['cotization_afp', 'apv', 'others_discounts', 'institution_health', 'price_plan']);
    
        // Actualiza los parámetros del trabajador
        Parameter::updateWorkerParametersInsurance($worker->id, $request->input('type'), $params, auth()->user()->school_id_session);
    
        return redirect()->route('insurances.index', ['type' => $request->input('type')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Insurance $insurance, Request $request)
    {
        $insurance->delete();

        // Captura el tipo de la solicitud y lo pasa al redirigir
        $type = $request->input('type');

        return redirect()->route('insurances.index', ['type' => $type]);
    }
}
