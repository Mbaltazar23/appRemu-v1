<?php

namespace App\Http\Controllers;

use App\Helpers\LiquidationHelper;
use App\Helpers\MonthHelper;
use App\Models\Liquidation;
use App\Models\TmpLiquidation;
use App\Models\Worker;
use Illuminate\Http\Request;

class LiquidationController extends Controller
{
    /**
     * Create the controller instance.
     *
     * This constructor method automatically applies resource authorization
     * for the Liquidation model, ensuring that only authorized users can
     * perform actions on liquidations.
     */
    public function __construct()
    {
        $this->authorizeResource(Liquidation::class, 'liquidation');
    }
    /**
     * Display a listing of the resource.
     *
     * This method returns the view with a list of worker types,
     * allowing users to select the type of worker for which
     * they want to generate a liquidation.
     */
    public function index()
    {
        $workerTypes = Worker::getWorkerTypes(); // Get worker types
        return view('liquidations.index', compact('workerTypes'));
    }
    /**
     * Show the worker selection view based on the worker type.
     *
     * This method retrieves workers based on the selected worker type
     * and their associated school, as well as distinct years of liquidation.
     *
     */
    public function selectWorkerType($workerType)
    {
        $school_id    = auth()->user()->school_id_session;
         // Get distinct years for liquidation
        $distincYears = Liquidation::getDistinctYears();
        // Retrieve workers by worker type and school ID
        $workers = Worker::where('worker_type', $workerType)
            ->where('school_id', $school_id)
            ->get();

        return view('liquidations.selectWorker', compact('workers', 'workerType', 'distincYears'));
    }

    /**
     * Show the liquidation details for a specific worker.
     *
     * This method fetches and displays existing liquidations for a given worker.
     * It also clears the temporary liquidation data before displaying.
     */
    public function workerLiquidation($workerId)
    {
        // Delete the temporary liquidation data if it exists
        TmpLiquidation::truncate();
        $worker       = Worker::findOrFail($workerId);
        $liquidations = Liquidation::where('worker_id', $workerId)->get();

        return view('liquidations.workerLiquidation', compact('worker', 'liquidations'));
    }
    /**
     * Create a new liquidation for a specific worker.
     *
     * This method checks if there is an existing liquidation for the worker in the current month
     * and year, and it allows the user to proceed with a new liquidation, if applicable.
     * It also validates the worker's AFP and ISAPRE (health coverage) status.
     */
    public function create($workerId)
    {
        // Get the authenticated user's school ID
        $school_id = auth()->user()->school_id_session; 
        // Get the worker by his id and instantiate the message to display
        $worker = Worker::findOrFail($workerId);
        $messageLiquidation = "";
        // Check if a liquidation already exists for the worker in the current month and year
        if (Liquidation::exists(now()->month, now()->year, $workerId)) {
            // If a liquidation exists, set a warning message
            $messageLiquidation = 'La liquidación existente será reemplazada en caso de que desee guardar esta';
            // Redirect if we are not already on the current view with the warning message
            if (! session()->has('warning')) {
                return redirect()->route('liquidations.create', ['workerId' => $workerId])
                    ->with('warning', $messageLiquidation); // Pass warning message to the view
            }
        }
        // Validate if the worker has AFP and ISAPRE (or Fonasa) status
        $liquidationHelper = new LiquidationHelper();
        $errorMessage = $liquidationHelper->checkAFPandISAPRE($workerId, $school_id);
        // If there is an error, redirect back with the error message
        if ($errorMessage) {
            return redirect()->back()
                ->withInput() // Optionally, keep the previous form values
                ->with('error', $errorMessage)
                ->with('workerId', $workerId); // Pass workerId as parameter
        }
        // Retrieve data for the liquidation
        $liquidationHelper->getLiquidationData($workerId, $school_id);
        // Get temporary liquidation data
        $tmp = TmpLiquidation::where('in_liquidation', 1)->get();
        // Return the view for creating a liquidation
        return view('liquidations.create', [
            'worker' => $worker,
            'tmp'    => $tmp,
        ])->with('warning', $messageLiquidation); // Pass the warning message directly to the view
    }
    /**
     * Store a newly created liquidation in the database.
     *
     * This method attempts to store the liquidation data based on the request.
     * If the storage is successful, the user is redirected to the worker's liquidation details.
     */
    public function store(Request $request, Worker $worker)
    {
        // Attempt to store the liquidation
        $liquidation = Liquidation::storeLiquidation($request, $worker->id);
        // Check if the liquidation was successfully created
        if (! $liquidation) {
            // If the liquidation could not be created (e.g., validation error)
            return redirect()->back()
                ->withInput() // Optionally, keep the previous form values
                ->with('error', "Hubo un error al momento de crear la liquidacion !!")
                ->with('workerId', $worker->id); // Pass workerId as parameter
        }
        // Redirect back to the 'workerLiquidation' view with success message
        return redirect()->route('liquidations.workerLiquidation', ['workerId' => $worker->id])->with('success', 'La liquidación se ha creado correctamente.');
    }
    /**
     * Get the "glosa" (HTML table) for a specific liquidation.
     *
     * This method retrieves the 'glosa' for the liquidation by its ID
     * and returns it as a response in HTML format.
     */
    public function getGlosa($id)
    {
        // Find the liquidation by its ID
        $liquidation = Liquidation::findOrFail($id);
        // Get the glosa (which contains the generated HTML table)
        $glosa = $liquidation->glosa;
        // Return the glosa as an HTML response
        return response($glosa);
    }
    /**
     * Print the glosas for liquidations based on the specified criteria.
     *
     * This method retrieves all liquidations for a given worker type,
     * month, and year and generates the corresponding glosas.
     */
    public function printGlosas(Request $request, $type)
    {
        // Get the authenticated user's school ID along with the year, month and the textual month converted into numerical data
        $school_id = auth()->user()->school_id_session; 
        $year      = $request->input('year');
        $month     = $request->input('month');
        $mountText = MonthHelper::integerToMonth($month);
        // Form validation: if the year is not valid, redirect with error
        if ($request->input('year') == 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Debe seleccionar un año para mostrar las liquidaciones.')
                ->with('workerType', $type);
        }
        // Get liquidations by type, month, year, and school
        $liquidations = Liquidation::getLiquidationsByType($month, $year, $type, $school_id);
        // Retrieve the glosas (HTML content) for each liquidation
        $glosas = [];
        foreach ($liquidations as $liquidation) {
            $glosas[] = $liquidation->glosa; // Assuming 'glosa' is the HTML content
        }
        // Pass the glosas to the view
        return view('liquidations.printGlosas', compact('glosas', 'mountText', 'year'));
    }
    /**
     * Delete a specific liquidation and redirect back.
     *
     * This method deletes a liquidation from the database and then
     * redirects the user back to the previous page, showing a success message.
     */
    public function destroy(Liquidation $liquidation, $workerId)
    {
        // Delete the liquidation
        $liquidation->delete();
        // Retrieve the worker and their liquidations after deletion
        $worker = Worker::findOrFail($workerId);
        $liquidations = Liquidation::where('worker_id', $workerId)->get();
        // Redirect back with success message
        return redirect()->back()->with('worker', $worker)->with('liquidations', $liquidations)
            ->with('success', "Liquidacion Eliminado Exitosamente !!");
    }

}
