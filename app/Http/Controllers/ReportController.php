<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use App\Models\Report;
use App\Models\School;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $typeInsurances = Report::getInsuranceTypesWithPermission();
          // Filter the insurance types based on the user's permissions
          $user = auth()->user();
          $accessibleInsurances = $typeInsurances->filter(function ($type) use ($user) {
              // Check if the user has permission for this insurance type
              return in_array($type['permission'], $user->role->permissions);
          });
        return view('reports.index', compact('accessibleInsurances'));
    }

    public function typeInsurance($typeInsurance)
    {
        $school_id = auth()->user()->school_id_session;

        $tuition_id = "AFPTRABAJADOR";
        if ($typeInsurance != Insurance::AFP) {
            $tuition_id = "ISAPRETRABAJADOR";
        }
        // Obtener los trabajadores por tipo y por escuela
        $insurancesType = Report::getDescriptionList($school_id, $tuition_id);
        //dd($insurancesType);
        return view('reports.typeInsurance', compact('insurancesType', 'typeInsurance'));
    }

    public function generateReport($typeInsurance, $month, $year, $insurance)
    {
        $school_id = auth()->user()->school_id_session;
        $school = School::find($school_id);
        // Llamamos al método estático y obtenemos los datos del reporte
        $reportData = Report::generateReportData($typeInsurance, $month, $year, $insurance, $school_id);
        // Instanciamos las variables 'data' y 'totals' por separado
        $data = $reportData['data'];
        $totals = $reportData['totals'];

        return view('reports.show', compact('data', 'totals', 'typeInsurance', 'insurance', 'school', 'month', 'year'));
    }

}
