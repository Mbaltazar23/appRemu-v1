<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use App\Models\Liquidation;
use App\Models\School;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function index()
    {
        // Get the available item options for cost centers
        $itemOptions = CostCenter::getItemOptions();
        
        // Get the available period options for cost centers
        $periodOptions = CostCenter::getPeriodOptions();
        
        // Get the distinct years from the liquidations
        $distincYears = Liquidation::getDistinctYears();
        
        // Get all schools that have associated users
        $schools = School::has('schoolUsers')->get();
        
        // Return the view with the available options and schools data
        return view('costcenters.index', compact('itemOptions', 'periodOptions', 'distincYears', 'schools'));
    }

    public function store(Request $request)
    {
        // Get the form inputs from the request
        $schoolId = $request->input('school');
        $item = $request->input('item');
        $periodo = $request->input('periodo');
        $year = $request->input('year');
        
        // Find the school by its ID
        $school = School::find($schoolId);
        
        // Get the total liquidation sums for the specified cost center and period
        $result = CostCenter::getLiquidationSumsTotalCosts($schoolId, $item, $periodo);
        
        // If everything is correct, pass the results to the `show` view to display in a modal window
        return view('costcenters.show', [
            'workers' => $result['workers'],
            'school' => $school,
            'titperiodo' => $periodo,
            'year' => $year,
        ]);
    }
}
