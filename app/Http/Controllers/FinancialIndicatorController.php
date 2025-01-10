<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialIndModRequest;
use App\Models\FinancialIndicator;
use App\Models\Operation;
use App\Models\Parameter;
use Illuminate\Http\Request;

class FinancialIndicatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $financial = new FinancialIndicator();
        $indices = $financial->getEconomicIndices();

        return view('financial_indicators.index', compact('indices', 'financial'));
    }

    public function show(Request $request)
    {
        $financialIndicator = new FinancialIndicator();
        $schoolId = auth()->user()->school_id_session;

        $index = $request->input('index');
        $values = [];
        $minLimits = [];
        $maxLimits = [];
        $impValues = [];
        $rebValues = [];
        $indices = $financialIndicator->getEconomicIndices();

        // Aquí asumo que tienes un método que obtiene los datos de corrección monetaria.
        $data = config('monetarycom.datos'); // Carga el arreglo de configuración.

        switch ($index) {
            case 'uf':
                $values = $financialIndicator->getCurrentValues();
                $values['uf'] = str_replace(',', '', number_format($values['uf'], 0, '.', '')); // Cambia la coma por un punto
                $values['utm'] = str_replace(',', '', number_format($values['utm'], 0, '.', '')); // Cambia la coma por un punto
                Parameter::createOrUpdateParamIndicators("UF", $values['uf']);
                Parameter::createOrUpdateParamIndicators("UTM", $values['utm']);
                break;

            case 'impuesto_renta':
                for ($i = 2; $i <= 8; $i++) {
                    $minLimits[$i] = Operation::getMinLimit("IMPUESTOTRAMO$i");
                    $maxLimits[$i] = Operation::getMaxLimit("IMPUESTOTRAMO$i");
                    $impValues[$i] = Parameter::getValueByName("FACTORIMPTRAMO$i", 0, 0);
                    $rebValues[$i] = Parameter::getValueByName("FACTORREBAJAIMPTRAMO$i", 0, 0);
                }
                break;

            case 'asignacion_familiar':
                for ($i = 1; $i <= 3; $i++) {
                    $minLimits[$i] = Operation::getMinLimit("FILTROASIGFAMT$i");
                    $maxLimits[$i] = Operation::getMaxLimit("FILTROASIGFAMT$i");
                    $impValues[$i] = Parameter::getValueByName("ASIGCAR.FAMTRAMO$i", 0, 0);
                }
                break;

            case 'correccion_monetaria':
                $data = config('monetarycom.datos'); // Obtiene los datos desde la configuración.
                break;

            default:
                return redirect()->route('financial-indicators.index')->with('error', 'Índice no válido.');
        }

        return view('financial_indicators.show', compact('index', 'values', 'minLimits', 'maxLimits', 'impValues', 'rebValues', 'indices', 'data', 'financialIndicator'));
    }

    public function modify(FinancialIndModRequest $request)
    {
        $schoolId = auth()->user()->school_id_session;
        $index = $request->input('index');
        if ($index === 'impuesto_renta') {
            for ($i = 2; $i <= 8; $i++) {
                Parameter::updateOrInsertParamValue("FACTORIMPTRAMO$i",0,0,"", $request->input("IMP$i"));
                Parameter::updateOrInsertParamValue("FACTORREBAJAIMPTRAMO$i",0,0,"", $request->input("REB$i"));
                Operation::updOrInsertTopesOperation(["IMPUESTOTRAMO$i"], $request->input("MIN$i"), $request->input("MAX$i"));
            }
        } else {
            for ($i = 1; $i <= 3; $i++) {
                Parameter::updateOrInsertParamValue("ASIGCAR.FAMTRAMO$i",0,0,"", $request->input("VAL$i"));
                Operation::updOrInsertTopesOperation(["FILTROASIGFAMT$i"], $request->input("MIN$i"), $request->input("MAX$i"));
            }
        }
        return redirect()->route('financial-indicators.show', compact('index'))
            ->with('success', 'Los valores han sido modificados con éxito');
    }
}
