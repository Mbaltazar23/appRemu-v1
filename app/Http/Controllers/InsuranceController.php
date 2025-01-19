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
        $type      = $request->input('type');

        $insuranceName = Insurance::getInsuranceTypes()[$type];
        $insurances    = Insurance::where('type', $type)->get();

        // Select the first insurance automatically if 'insurance_id' is not passed
        $insurance = $insurances->firstWhere('id', $request->input('insurance_id')) ?? $insurances->first();

        // If no insurances are available, redirect with a message
        if (! $insurance) {
            return redirect()->route('insurances.index', ['type' => $type])
                ->with('warning', 'No hay seguros disponibles para este tipo.');
        }

        $workers = Worker::where(
            $insurance->type == Insurance::AFP ? 'insurance_AFP' : 'insurance_ISAPRE',
            $insurance->id
        )->get();

        $worker_id        = $request->input('worker_id') ?? optional($workers->first())->id;
        $workerParameters = $worker_id ? $this->getWorkerParameters($worker_id, $type) : [];

        $fields = $this->getFieldsParameters($insurance, $type, $workerParameters);

        return view('insurances.index', compact(
            'insurances',
            'type',
            'workers',
            'worker_id',
            'insurance',
            'workerParameters',
            'insuranceName',
            'fields'
        ));
    }

    protected function getWorkerParameters($worker_id, $type)
    {
        $worker = Worker::find($worker_id);

        return Parameter::getWorkerParametersByInsuranceType($worker, $type);
    }

    protected function getFieldsParameters($insurance, $type, $workerParameters)
    {
        // Make sure workerParameters has the correct values
        if (empty($workerParameters)) {
            // If no parameters, return an array with default or empty values
            return [];
        }
        return $type == \App\Models\Insurance::AFP
        ? [
            ['label' => "Cotización Legal (%)", 'name' => 'cotizacionafp', 'value' => $workerParameters['cotizacionafp'], 'readonly' => true],
            ['label' => 'APV', 'name' => 'apv', 'value' => $workerParameters['apv']],
            ['label' => 'Tipo de Moneda APV', 'name' => 'unit', 'options' => ['Pesos', 'UF'], 'selected' => $workerParameters['unidad']],
            ['label' => 'Otros Descuentos (en pesos)', 'name' => 'others_discounts', 'value' => $workerParameters['others_discounts']],
        ]
        : [
            ['label' => "Cotización Legal (%)", 'name' => 'cotizacionisapre', 'value' => $workerParameters['cotizacionisapre'], 'readonly' => true],
            ['label' => 'Cotización Pactada', 'name' => 'cotization', 'value' => $workerParameters['cotizacionpactada']],
            ['label' => 'Tipo de moneda Cotización', 'name' => 'unit', 'options' => ['Pesos', 'UF'], 'selected' => $workerParameters['unidad']],
            ['label' => 'Otros Descuentos (en pesos)', 'name' => 'others_discounts', 'value' => $workerParameters['others_discounts']],
        ];
    }

    /** View Link Worker */
    public function linkWorker(Request $request, $insuranceId)
    {
        $school_id = auth()->user()->school_id_session;
        $insurance = Insurance::findOrFail($insuranceId);
        $type      = $insurance->type;

        // Get workers from the school_id who are not linked to an insurance of the same type
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

        $worker    = Worker::findOrFail($request->worker_id);
        $school_id = auth()->user()->school_id_session;
        // Determine the insurance type and the parameter title
        $paramTitle = $request->input('type') != Insurance::AFP ? "ISAPRETRABAJADOR" : "AFPTRABAJADOR";
        $Insurance  = $paramTitle != 'ISAPRETRABAJADOR' ? 'AFP' : 'Institución';
        // Check if the worker is already associated with an insurance of the same type
        $existingInsurance = Parameter::getParameterValue($paramTitle, $worker->id, $school_id);
        // If the worker already has an insurance of the same type and we are not forcing the update
        if ($existingInsurance != 0 && $request->input('force_update') !== 'true') {
            // Send JSON response for confirmation
            return response()->json([
                'confirm' => true,
                'message' => 'Este trabajador ya está en otro ' . $Insurance . '. ¿Desea eliminar la anterior designación y asignarla a esta?',
            ]);
        }
        // If no conflicts, link the worker to the insurance
        if ($request->input('type') != Insurance::ISAPRE) {
            $worker->insurance_AFP = $insuranceId;
        } else {
            $worker->insurance_ISAPRE = $insuranceId;
        }
        // Update the field associated with the Insurance
        $worker->save();
        // Update parameters
        $data = Parameter::updateOrInsertInsuranceParams(
            $request->worker_id,
            $request->input('type'),
            $school_id,
            $insuranceId
        );
        // Send success response if no conflicts
        return response()->json([
            'confirm' => false,
            'message' => $data,
        ]);
    }

    // Method to handle parameter updates or unlinking
    public function setParameters(Request $request)
    {
        // Get form values begin to The worker's ID
        $workerId = $request->worker_id;    
        // The school's ID    
        $schoolId = auth()->user()->school_id_session;
        // Insurance type (AFP or ISAPRE)
        $insuranceType = $request->type; 
        // Operation: 'modificar' or 'desvincular'                   
        $operation = $request->operation;               
          // Check if the operation is "desvincular"                                                  
        if ($operation == 'desvincular') {
            // Unlink parameters (Delete)
            Parameter::deleteParameters($workerId, $schoolId, $insuranceType);
            return redirect()->back()
                ->withInput() // This is optional but keeps previous form values.
                ->with('success', "Trabajador desvinculado Exitosamente !!")
                ->with('insurance_id', $request->input('insurance_id'));

        }
        // Check if the operation is "modificar"
        if ($operation == 'modificar') {
            // Get necessary values for validation
            $unitCotizacion = $insuranceType == Insurance::AFP ?? $request->input('unit');
            $cotizacion     = $insuranceType == Insurance::AFP ? $request->input('apv') : $request->input('cotization');
            // Validate quotations (if unit is UF)
            $validationResult = Parameter::checkCotizationUnitLimit(
                $workerId,
                $schoolId,
                $unitCotizacion,
                $cotizacion,
                $insuranceType
            );

            if ($validationResult['status'] == 'error') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $validationResult['message']);
            }
            // Modify or add parameters
            Parameter::updateOrInsertInsuranceParams($workerId, $insuranceType, $schoolId, $request->input('insurance_id'), $request);
            return redirect()->back()
                ->withInput() // This is optional but keeps previous form values.
                ->with('success', "Montos Actualizados Exitosamente !!")
                ->with('worker_id', $workerId)
                ->with('insurance_id', $request->input('insurance_id'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $insurance = new Insurance();
        // Capture the type from the request
        $type          = $request->input('type');
        $insuranceName = Insurance::getInsuranceTypes()[$type];

        return view('insurances.create', compact('insurance', 'type', 'insuranceName'));
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
        // Capture the type from the request
        $type = $request->input('type');
        // Get the types of insurance
        $insuranceTypes = Insurance::getInsuranceTypes();
        // Define the title of the insurance type
        $insuranceName = Insurance::getInsuranceTypes()[$type];

        return view('insurances.show', compact('insurance', 'type', 'insuranceTypes', 'insuranceName'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Insurance $insurance, Request $request)
    {
        // Capture the type from the request
        $type = $request->input('type');
        // Define the title of the insurance type
        $insuranceName = Insurance::getInsuranceTypes()[$type];

        return view('insurances.edit', compact('insurance', 'type', 'insuranceName'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InsuranceFormRequest $request, Insurance $insurance)
    {
        $insurance->update($request->validated());

        // Capture the type from the request and pass it for redirection
        $type = $request->input('type');

        return redirect()->route('insurances.index', ['type' => $type]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Insurance $insurance, Request $request)
    {
        $insurance->delete();
        // Capture the type from the request and pass it for redirection
        $type = $request->input('type');

        return redirect()->route('insurances.index', ['type' => $type]);
    }
}
