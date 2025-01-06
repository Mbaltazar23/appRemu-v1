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
        $itemOptions = CostCenter::getItemOptions();
        $periodOptions = CostCenter::getPeriodOptions();
        $distincYears = Liquidation::getDistinctYears();
        $schools = School::has('schoolUsers')->get();
        return view('costcenters.index', compact('itemOptions', 'periodOptions', 'distincYears', 'schools'));
    }

    public function store(Request $request)
    {
        // Obtenemos los inputs del formulario
        $schoolId = $request->input('school');
        $item = $request->input('item');
        $periodo = $request->input('periodo');
        $year = $request->input('year');

        // Obtener el colegio (school) correspondiente
        $school = School::find($schoolId);
        // Obtener los totales de liquidación
        $result = CostCenter::getLiquidationSumsTotalCosts($schoolId, $item, $periodo);
        // Si todo es correcto, pasamos los resultados a la vista `show` para mostrar en la ventana emergente
        return view('costcenters.show', [
            'workers' => $result['workers'],
            'school' => $school,
            'titperiodo' => $periodo,
            'year' => $year,
        ]);
    }

}
