<?php
namespace App\Http\Controllers;

use App\Models\Insurance;
use App\Models\Report;
use App\Models\School;

class ReportController extends Controller
{
    public function __construct()
    {
        // Ensures that the user is authenticated before accessing any of the report-related actions
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the types of insurances with the corresponding permissions
        $typeInsurances = Report::getInsuranceTypesWithPermission();

        // Get the authenticated user
        $user = auth()->user();

        // Filter the insurance types based on the user's permissions
        $accessibleInsurances = $typeInsurances->filter(function ($type) use ($user) {
            // Check if the user has permission for this insurance type
            return in_array($type['permission'], $user->role->permissions);
        });

        // Return the view with the accessible insurances
        return view('reports.index', compact('accessibleInsurances'));
    }

    public function typeInsurance($typeInsurance)
    {
        // Get the school ID from the user's session
        $school_id     = auth()->user()->school_id_session;
        $nameInsurance = Insurance::getInsuranceTypes()[$typeInsurance];
        // Set the tuition ID depending on the type of insurance
        $tuition_id = "AFPTRABAJADOR";
        if ($typeInsurance != Insurance::AFP) {
            $tuition_id = "ISAPRETRABAJADOR";
        }

        // Get the list of descriptions for the workers based on the school and insurance type
        $insurancesType = Report::getDescriptionList($school_id, $tuition_id);

        // Return the view with the insurance descriptions and selected insurance type
        return view('reports.typeInsurance', compact('insurancesType', 'typeInsurance', 'nameInsurance'));
    }

    public function generateReport($typeInsurance, $month, $year, $insurance)
    {
        // Get the school ID from the user's session
        $school_id = auth()->user()->school_id_session;

        // Find the school by ID
        $school = School::find($school_id);

        // Call the static method to generate the report data based on the provided parameters
        $reportData = Report::generateReportData($typeInsurance, $month, $year, $insurance, $school_id);

        // Separate the report data into 'data' and 'totals' variables
        $data   = $reportData['data'];
        $totals = $reportData['totals'];

        // Return the report view with the data, totals, and other relevant parameters
        return view('reports.show', compact('data', 'totals', 'typeInsurance', 'insurance', 'school', 'month', 'year'));
    }
}
