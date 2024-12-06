<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use App\Models\Liquidation;
use App\Models\Parameter;
use App\Models\Report;
use App\Models\School;
use Illuminate\Http\Request;

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
        $typeInsurances = Insurance::getInsuranceTypes();
        return view('reports.index', compact('typeInsurances'));
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
    
        $tuition_id = "AFPTRABAJADOR";
        if ($typeInsurance != Insurance::AFP) {
            $tuition_id = "ISAPRETRABAJADOR";
        }
        // Recuperar el detalle de las liquidaciones según el tipo de seguro
        $details = Report::getDetailInsurance($tuition_id, $insurance, $school_id, $month, $year);

        // Variables para los totales
        $totals = [
            'total_income' => 0,
            'total_contribution' => 0,
            'total_voluntary_contribution' => 0,
            'total_affiliate_contribution' => 0,
            'total_employer_contribution' => 0,
            'total_health_fund' => 0,
            'total_additional_health' => 0,
            'total_payment' => 0,
        ];

        // Preparar los datos para pasar a la vista
        $data = [];

        // Tabla de reporte según el tipo de seguro
        if ($typeInsurance == Insurance::AFP) {
            foreach ($details as $row) {
                // Usamos el método getDetailByTuitionId() para obtener los valores de la liquidación
                $taxable_income = Liquidation::getDetailByTuitionId($row->liquidation_id, "RENTAIMPONIBLE", "value");
                $contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "AFP", "value");
                $voluntary_contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "APV", "value");
                $affiliate_contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "SEGUROCESANTIA", "value");
                $income = str_replace(",", "", $taxable_income);

                // Si el tipo de contrato es 2 y no tiene seguro, calcular seguro empleador
                $contract_type = Parameter::getParameterValue("TIPOCONTRATO", $row->worker_id, $school_id);
                $has_insurance = Parameter::getParameterValue('ADHIEREASEGURO', $row->worker_id, $school_id);
                if ($contract_type == 2 && $has_insurance == 0) {
                    $employer_contribution = $income * (0.03 - 0.006);
                    $employer_contribution = number_format($employer_contribution, 0, 0, ",");
                } else {
                    $employer_contribution = 0;
                }

                // Sumar totales
                $totals['total_income'] += $income;
                $totals['total_contribution'] += str_replace(",", "", $contribution);
                $totals['total_voluntary_contribution'] += str_replace(",", "", $voluntary_contribution);
                $totals['total_affiliate_contribution'] += str_replace(",", "", $affiliate_contribution);
                $totals['total_employer_contribution'] += str_replace(",", "", $employer_contribution);

                // Agregar los datos de cada trabajador a la variable de datos
                $data[] = [
                    'id_number' => $row->rut,
                    'full_name' => $row->name . ' ' . $row->last_name,
                    'taxable_income' => $taxable_income,
                    'contribution' => $contribution,
                    'voluntary_contribution' => $voluntary_contribution,
                    'affiliate_contribution' => $affiliate_contribution,
                    'employer_contribution' => $employer_contribution,
                ];
            }

        } else {
            foreach ($details as $row) {
                // Usamos el método getDetailByTuitionId() para obtener los valores de la liquidación
                $taxable_income = Liquidation::getDetailByTuitionId($row->liquidation_id, "RENTAIMPONIBLE", "value");
                $contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "SALUD", "value");
                $additional_health = Liquidation::getDetailByTuitionId($row->liquidation_id, "ADICIONALSALUD", "value");
                $additional_health = str_replace(",", "", $additional_health);
                $income = str_replace(",", "", $taxable_income);
                // Calcular fondo salud (7%) y total a pagar
                $health_fund = $income * 0.07;
                $total_contribution = str_replace(",", "", $contribution) + $additional_health;
                $total_payment = $total_contribution;

                // Sumar totales
                $totals['total_income'] += $income;
                $totals['total_contribution'] += str_replace(",", "", $contribution) + $additional_health;
                $totals['total_additional_health'] += $additional_health;
                $totals['total_health_fund'] += $health_fund;
                $totals['total_payment'] += $total_contribution;

                // Agregar los datos de cada trabajador a la variable de datos
                $data[] = [
                    'id_number' => $row->rut,
                    'full_name' => $row->name . ' ' . $row->last_name,
                    'taxable_income' => $taxable_income,
                    'health_fund' => number_format($health_fund, 0, 0, ","),
                    'additional_health' => number_format($additional_health, 0, 0, ","),
                    'total_contribution' => number_format($total_contribution, 0, 0, ","),
                    'total_payment' => number_format($total_payment, 0, 0, ","),
                ];
            }
        }

        // Pasamos los datos a la vista
        return view('reports.show', compact('data', 'totals', 'typeInsurance', 'insurance', 'school', 'month', 'year'));
    }

}
