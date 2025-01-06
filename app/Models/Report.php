<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    public static function getDescriptionList($school_id, $tuition_id)
    {
        return Parameter::leftJoin('insurances', 'parameters.description', '=', 'insurances.id') // Realiza el JOIN
            ->where('parameters.name', $tuition_id) // Filtra por el nombre del parámetro
            ->where('parameters.school_id', $school_id) // Filtra por el school_id
            ->select('parameters.description', 'insurances.name') // Selecciona los campos description y name
            ->groupBy('parameters.description', 'insurances.name') // Agrupa por description e insurance name
            ->get(); // Obtiene los resultados
    }

    public static function getDetailInsurance($parameter, $idInsurance, $school_id, $mount, $year)
    {
        return Worker::leftJoin('parameters', 'workers.id', '=', 'parameters.worker_id')
            ->leftJoin('liquidations', 'workers.id', '=', 'liquidations.worker_id')
            ->where('parameters.name', $parameter)
            ->where('parameters.description', $idInsurance)
            ->where('workers.school_id', $school_id)
            ->where('liquidations.month', $mount)
            ->where('liquidations.year', $year)
            ->select('workers.id as worker_id', 'liquidations.id as liquidation_id', 'workers.rut', 'workers.name', 'workers.last_name')
            ->get();
    }

    /**
     * Método para generar el reporte según el tipo de seguro.
     */
    public static function generateReportData($typeInsurance, $month, $year, $insurance, $school_id)
    {
        $tuition_id = $typeInsurance == Insurance::AFP ? "AFPTRABAJADOR" : "ISAPRETRABAJADOR";
        $details = self::getDetailInsurance($tuition_id, $insurance, $school_id, $month, $year);

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

        $data = [];

        foreach ($details as $row) {
            $taxable_income = Liquidation::getDetailByTuitionId($row->liquidation_id, "RENTAIMPONIBLE", "value");
            $income = str_replace(",", "", $taxable_income);

            if ($typeInsurance == Insurance::AFP) {
                $contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "AFP", "value");
                $voluntary_contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "APV", "value");
                $affiliate_contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "SEGUROCESANTIA", "value");

                $contract_type = Parameter::getParameterValue("TIPOCONTRATO", $row->worker_id, $school_id);
                $has_insurance = Parameter::getParameterValue('ADHIEREASEGURO', $row->worker_id, $school_id);
                $employer_contribution = ($contract_type == 2 && $has_insurance == 0)
                ? $income * (0.03 - 0.006)
                : 0;

                // Sumando las contribuciones
                $totals['total_contribution'] += str_replace(",", "", $contribution);
                $totals['total_voluntary_contribution'] += str_replace(",", "", $voluntary_contribution);
                $totals['total_affiliate_contribution'] += str_replace(",", "", $affiliate_contribution);
                $totals['total_employer_contribution'] += str_replace(",", "", $employer_contribution);

                $data[] = [
                    'id_number' => $row->rut,
                    'full_name' => $row->name . ' ' . $row->last_name,
                    'taxable_income' => $taxable_income,
                    'contribution' => $contribution,
                    'voluntary_contribution' => $voluntary_contribution,
                    'affiliate_contribution' => $affiliate_contribution,
                    'employer_contribution' => $employer_contribution,
                ];
            } else {
                $contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "SALUD", "value");
                $additional_health = str_replace(",", "", Liquidation::getDetailByTuitionId($row->liquidation_id, "ADICIONALSALUD", "value"));
                $health_fund = $income * 0.07;
                $total_contribution = str_replace(",", "", $contribution) + $additional_health;

                // Sumando los totales
                $totals['total_additional_health'] += $additional_health;
                $totals['total_health_fund'] += $health_fund;
                $totals['total_payment'] += $total_contribution;

                $data[] = [
                    'id_number' => $row->rut,
                    'full_name' => $row->name . ' ' . $row->last_name,
                    'taxable_income' => $taxable_income,
                    'health_fund' => number_format($health_fund, 0, 0, ","),
                    'additional_health' => number_format($additional_health, 0, 0, ","),
                    'total_contribution' => number_format($total_contribution, 0, 0, ","),
                ];
            }

            // Sumar los ingresos totales
            $totals['total_income'] += $income;
        }

        // Retornar los resultados como un array
        return [
            'data' => $data,
            'totals' => $totals,
        ];
    }
}
