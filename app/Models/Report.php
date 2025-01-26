<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model {

    use HasFactory;

    /**
     * Get all insurance types with the permission field.
     *
     * This method fetches the insurance types from the Insurance model
     * and adds the 'permission' field to each insurance type for user authorization.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getInsuranceTypesWithPermission() {
        // Retrieve all insurance types and add the 'permission' field
        return collect(Insurance::TYPES)->map(function ($type, $key) {
                    return [
                        'name' => $type, // The name of the insurance type (e.g., 'AFP', 'ISAPRE')
                        'permission' => self::getPermissionForInsuranceType($key), // Add the corresponding permission for each type
                    ];
                });
    }

    /**
     * Get the permission for a given insurance type.
     *
     * This helper method returns the appropriate permission string
     * based on the insurance type constant.
     *
     * @param int $typeKey - The type key (e.g., 1 for AFP, 2 for ISAPRE).
     * @return string - The permission string for the insurance type.
     */
    private static function getPermissionForInsuranceType($typeKey) {
        // Define permissions for each insurance type (customize as needed)
        $permissions = [
            Insurance::AFP => 'CONPREVAFP', // Example permission for AFP
            Insurance::ISAPRE => 'CONPREVISAPRE', // Example permission for ISAPRE
                // Insurance::FONASA => 'CONPREVONASA', // Uncomment and define for FONASA if needed
        ];
        // Return the permission for the given insurance type key
        return $permissions[$typeKey] ?? ''; // Return empty string if no permission defined
    }

    /**
     * Get the description list for a specific tuition and school.
     *
     * @param int $school_id - School ID.
     * @param int $tuition_id - Tuition ID.
     * @return \Illuminate\Database\Eloquent\Collection - Collection of parameters and insurance details.
     */
    public static function getDescriptionList($school_id, $tuition_id) {
        return Parameter::leftJoin('insurances', 'parameters.description', '=', 'insurances.id') // Perform the JOIN
                        ->where('parameters.name', $tuition_id) // Filter by the parameter name
                        ->where('parameters.school_id', $school_id) // Filter by school_id
                        ->select('parameters.description', 'insurances.name') // Select the description and name fields
                        ->groupBy('parameters.description', 'insurances.name') // Group by description and insurance name
                        ->get(); // Get the results
    }

    /**
     * Get the insurance details for a given parameter, insurance, and time period.
     *
     * @param string $parameter - Parameter name.
     * @param int $idInsurance - Insurance ID.
     * @param int $school_id - School ID.
     * @param int $mount - Month.
     * @param int $year - Year.
     * @return \Illuminate\Database\Eloquent\Collection - Collection of workers' details.
     */
    public static function getDetailInsurance($parameter, $idInsurance, $school_id, $mount, $year) {
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
     * Method to generate the report based on insurance type.
     *
     * @param int $typeInsurance - Type of insurance (AFP or Isapre).
     * @param int $month - Month of the report.
     * @param int $year - Year of the report.
     * @param string $insurance - Insurance description.
     * @param int $school_id - School ID.
     * @return array - Data and totals of the report.
     */
    public static function generateReportData($typeInsurance, $month, $year, $insurance, $school_id) {
        // Set tuition_id based on the type of insurance
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
            'total_voluntary_contribution' => 0,
            'total_payment' => 0,
        ];

        $data = [];

        foreach ($details as $row) {
            $taxable_income = Liquidation::getDetailByTuitionId($row->liquidation_id, "RENTAIMPONIBLE", "value");
            $income = str_replace(",", "", $taxable_income);

            if ($typeInsurance == Insurance::AFP) {
                // AFP specific contributions
                $contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "AFP", "value");
                $voluntary_contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "APV", "value");
                $affiliate_contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "SEGUROCESANTIA", "value");

                // Contract type and insurance affiliation check
                $contract_type = Parameter::getParameterValue("TIPOCONTRATO", $row->worker_id, $school_id);
                $has_insurance = Parameter::getParameterValue('ADHIEREASEGURO', $row->worker_id, $school_id);
                $employer_contribution = ($contract_type == 2 && $has_insurance == 0) ? $income * (0.03 - 0.006) : 0;

                // Summing up the contributions
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
                    'employer_contribution' => number_format($employer_contribution, 0, 0, ","),
                ];
            } else {
                // Isapre specific contributions
                $contribution = Liquidation::getDetailByTuitionId($row->liquidation_id, "SALUD", "value");
                $additional_health = str_replace(",", "", Liquidation::getDetailByTuitionId($row->liquidation_id, "ADICIONALSALUD", "value"));
                $health_fund = $income * 0.07;
                $total_contribution = str_replace(",", "", $contribution) + $additional_health;
                $total_voluntary = str_replace(",", "", $contribution) + $additional_health;

                // Summing the totals
                $totals['total_health_fund'] += $health_fund;
                $totals['total_additional_health'] += $additional_health;
                $totals['total_voluntary_contribution'] += $total_voluntary;
                $totals['total_payment'] += $total_contribution;

                $data[] = [
                    'id_number' => $row->rut,
                    'full_name' => $row->name . ' ' . $row->last_name,
                    'taxable_income' => $taxable_income,
                    'health_fund' => number_format($health_fund, 0, 0, ","),
                    'additional_health' => number_format($additional_health, 0, 0, ","),
                    'voluntary_contribution' => number_format($total_voluntary, 0, 0, ','),
                    'total_contribution' => number_format($total_contribution, 0, 0, ","),
                ];
            }
            // Sum the total income
            $totals['total_income'] += $income;
        }
        // Return the results as an array
        return [
            'data' => $data,
            'totals' => $totals,
        ];
    }

}
