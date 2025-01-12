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
        $school_id = auth()->user()->school_id_session;
        $type = $request->input('type');
        // Obtener los seguros según el tipo
        $insurances = Insurance::where('type', $type)->get();
        // Asegurarse de que hay seguros disponibles antes de continuar
        $insurance = null;
        // Si hay seguros disponibles, proceder con la selección del seguro
        if ($insurances->isNotEmpty()) {
            // Obtener el seguro basado en el ID enviado por la solicitud o el primero disponible
            $insurance_id = $request->input('insurance_id');

            // Si se pasa un ID de seguro, buscar ese seguro, de lo contrario, usar el primero de la lista
            if ($insurance_id) {
                $insurance = Insurance::find($insurance_id);
            }
            // Si no se encuentra el seguro por ID, usar el primero de la lista
            if (!$insurance) {
                $insurance = $insurances->first();
            }
        }
        // Filtrar trabajadores según el tipo de seguro, solo si existe un seguro
        $workers = collect(); // Inicializamos como colección vacía por si no hay trabajadores
        if ($insurance) {
            if ($insurance->type == Insurance::AFP) {
                $workers = Worker::where('insurance_AFP', $insurance->id)->get(); // Esto devuelve una colección de trabajadores
            } else {
                $workers = Worker::where('insurance_ISAPRE', $insurance->id)->get(); // Esto también devuelve una colección
            }
        }
        // Obtener el worker_id si existe en la solicitud
        $worker_id = $request->input('worker_id');
        // Retornar la vista con las variables necesarias
        return view('insurances.index', compact('insurances', 'type', 'workers', 'worker_id', 'insurance'));
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

        return view('insurances.partials.link_worker', compact('insurance', 'workers', 'type'));
    }

    /** Send Link Worker To Insurance */
    public function attachWorker(Request $request, $insuranceId)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
        ]);

        $worker = Worker::findOrFail($request->worker_id);
        $school_id = auth()->user()->school_id_session;
        // Determinar el tipo de seguro y el título del parámetro
        $paramTitle = $request->input('type') != Insurance::AFP ? "ISAPRETRABAJADOR" : "AFPTRABAJADOR";
        $Insurance = $paramTitle != 'ISAPRETRABAJADOR' ? 'AFP' : 'institución';
        // Verificar si el trabajador ya está asociado con un seguro del mismo tipo
        $existingInsurance = Parameter::getParameterValue($paramTitle, $worker->id, $school_id);
        // Si el trabajador ya tiene un seguro del mismo tipo y no forzamos la actualización
        if ($existingInsurance != 0 && $request->input('force_update') !== 'true') {
            // Enviar respuesta JSON para la confirmación
            return response()->json([
                'confirm' => true,
                'message' => 'Este trabajador ya está en otro ' . $Insurance . '. ¿Desea eliminar la anterior designación y asignarla a esta?',
            ]);
        }
        // Si no hay conflictos, vinculamos al trabajador al seguro
        if ($request->input('type') != Insurance::ISAPRE) {
            $worker->insurance_AFP = $insuranceId;
        } else {
            $worker->insurance_ISAPRE = $insuranceId;
        }
        // Actualizar el campo asociado al Insurance
        $worker->save();
        // Actualización de parámetros
        $data = Parameter::updateOrInsertInsuranceParams(
            $request->worker_id,
            $request->input('type'),
            $school_id,
            $insuranceId
        );
        // Enviar éxito si no hay conflicto
        return response()->json([
            'confirm' => false,
            'message' => $data,
        ]);
    }

    // Método para manejar la actualización de parámetros o desvinculación
    public function setParameters(Request $request)
    {
        // Obtener los valores del formulario
        $workerId = $request->worker_id; // El ID del trabajador
        $schoolId = auth()->user()->school_id_session; // El ID de la escuela
        $insuranceType = $request->type; // Tipo de seguro (AFP o ISAPRE)
        $operation = $request->operation; // Operación: 'modificar' o 'desvincular'
        // Verificar si la operación es "desvincular"
        if ($operation == 'desvincular') {
            // Desvincular parámetros (Eliminar)
            Parameter::deleteParameters($workerId, $schoolId, $insuranceType);
            return redirect()->back()
                ->withInput() // Esto es opcional, pero mantiene los valores del formulario previo.
                ->with('success', "Trabajador desvinculado Exitosamente !!")
                ->with('insurance_id', $request->input('insurance_id'));

        }
        // Verificar si la operación es "modificar"
        if ($operation == 'modificar') {
            // Modificar o agregar parámetros
            Parameter::updateOrInsertInsuranceParams($workerId, $insuranceType, $schoolId, $request->input('insurance_id'), $request);
            return redirect()->back()
                ->withInput() // Esto es opcional, pero mantiene los valores del formulario previo.
                ->with('success', "Montos Actualizados Exitosamente !!")
                ->with('worker_id', $workerId)
                ->with('insurance_id', $request->input('insurance_id'));
        }
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
