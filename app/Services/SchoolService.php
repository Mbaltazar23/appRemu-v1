<?php

namespace App\Services;

use App\Models\Bonus;
use App\Models\Operation;
use App\Models\Parameter;
use App\Models\Template;
use App\Models\Tuition;
use App\Models\Worker;

class SchoolService
{
    /**
     * Insert parameters related to schools.
     *
     * @param array $schoolIds
     * @return void
     */
    public function handleSchoolParameters(array $schoolIds)
    {
        foreach ($schoolIds as $schoolId) {
            // Generate a random month and assign the value of CIERREMES
            $randomMonth    = rand(1, 12);
            $cierremes      = ($randomMonth == 2) ? 28 : rand(30, 31); // Asignar el valor de CIERREMES
            $paramsToInsert = [
                'CIERREMES' => $cierremes,
                'VALORIMD'  => rand(1, 1000),
            ];

            // Insert operations, tuitions, bonuses, ley operations, templates, parameters, tax brackets, family allowances, and costs
            $this->insertOperations($schoolId);
            $this->insertTuitions($schoolId);
            $this->insertBonuses($schoolId);
            $this->generateOperationsLeys($schoolId);
            $this->insertTemplates($schoolId);
            $this->insertParameters($paramsToInsert, $schoolId);
            $this->insertImpuestoTramos($schoolId);
            $this->insertAsignacionFamiliar($schoolId);
            $this->insertCostos($schoolId);
        }
    }

    /**
     * Insert parameters into the database.
     *
     * @param array $paramsToInsert
     * @param int $schoolId
     * @return void
     */
    private function insertParameters(array $paramsToInsert, int $schoolId)
    {
        foreach ($paramsToInsert as $name => $value) {
            Parameter::factory()->create([
                'name'      => $name,
                'school_id' => $schoolId,
                'value'     => $value,
            ]);
        }
    }

    /**
     * Insert tax brackets for a school.
     *
     * @param int $schoolId
     * @return void
     */
    private function insertImpuestoTramos(int $schoolId)
    {
        for ($i = 2; $i <= 8; $i++) {
            $this->insertImpuestoTramo($i, $schoolId);
        }
    }

    /**
     * Insert a specific tax bracket for a school.
     *
     * @param int $i
     * @param int $schoolId
     * @return void
     */
    private function insertImpuestoTramo(int $i, int $schoolId)
    {
        $impuestos = $this->getImpuestoValues($i);
        $this->createTuition("FACTORIMPTRAMO$i", "Factor Impuesto tramo $i", $schoolId);
        $this->createTuition("FACTORREBAJAIMPTRAMO$i", "Factor Rebaja Impuesto tramo $i", $schoolId);
        $this->createOperationImpTram($i, $schoolId, $impuestos['min'], $impuestos['max']);
    }

    /**
     * Get tax values for a specific bracket.
     *
     * @param int $i
     * @return array
     */
    private function getImpuestoValues(int $i): array
    {
        switch ($i) {
            case 2:return ['impuesto' => 0.04, 'rebaja' => 0.539, 'min' => 13.5, 'max' => 30];
            case 3:return ['impuesto' => 0.08, 'rebaja' => 1.737, 'min' => 30, 'max' => 50];
            case 4:return ['impuesto' => 0.14, 'rebaja' => 4.481, 'min' => 50, 'max' => 70];
            case 5:return ['impuesto' => 0.23, 'rebaja' => 11.117, 'min' => 70, 'max' => 90];
            case 6:return ['impuesto' => 0.30, 'rebaja' => 17.765, 'min' => 90, 'max' => 120];
            case 7:return ['impuesto' => 0.35, 'rebaja' => 23.272, 'min' => 120, 'max' => 120];
            case 8:return ['impuesto' => 0.4, 'rebaja' => 38.743, 'min' => 120, 'max' => 99.999];
            default:return ['impuesto' => 0.04, 'rebaja' => 38.743, 'min' => 13.5, 'max' => 99.999];
        }
    }

    /**
     * Create a tuition record.
     *
     * @param string $tuitionId
     * @param string $title
     * @param int $schoolId
     * @return void
     */
    private function createTuition(string $tuitionId, string $title, int $schoolId)
    {
        Tuition::factory()->create([
            'tuition_id' => $tuitionId,
            'title'      => $title,
            'type'       => 'P',
            'school_id'  => $schoolId,
        ]);
    }

    /**
     * Create an operation for a tax bracket.
     *
     * @param int $i
     * @param int $schoolId
     * @param float $minLimit
     * @param float $maxLimit
     * @return void
     */
    private function createOperationImpTram(int $i, int $schoolId, float $minLimit, float $maxLimit)
    {
        foreach ([1, 2] as $workerType) {
            Operation::factory()->create([
                'tuition_id'  => "IMPUESTOTRAMO$i",
                'operation'   => 'REMUNERACIONTRIBUTABLE',
                'worker_type' => $workerType,
                'limit_unit'  => 'UTM',
                'min_limit'   => $minLimit,
                'max_limit'   => $maxLimit,
                'application' => 111111111111,
                'school_id'   => $schoolId,
            ]);
        }

        Tuition::factory()->create([
            'tuition_id' => "IMPUESTOTRAMO$i",
            'title'      => "IMPUESTOTRAMO$i",
            'type'       => 'O',
            'school_id'  => $schoolId,
        ]);
    }

    /**
     * Insert family allowances for a school.
     *
     * @param int $schoolId
     * @return void
     */
    private function insertAsignacionFamiliar(int $schoolId)
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->insertAsignacionFamiliarTramo($i, $schoolId);
        }
    }

    /**
     * Insert a specific family allowance bracket for a school.
     *
     * @param int $i
     * @param int $schoolId
     * @return void
     */
    private function insertAsignacionFamiliarTramo(int $i, int $schoolId)
    {
        $asignacion = $this->getAsignacionValues($i);

        Tuition::factory()->create([
            'tuition_id' => "ASIGCAR.FAMTRAMO$i",
            'title'      => "Asignacion familiar tramo $i",
            'type'       => 'P',
            'school_id'  => $schoolId,
        ]);
        $this->createAsignacionOperation($i, $schoolId, $asignacion['min'], $asignacion['max']);
    }

    /**
     * Get family allowance values for a specific bracket.
     *
     * @param int $i
     * @return array
     */
    private function getAsignacionValues(int $i): array
    {
        switch ($i) {
            case 1:return ['valor' => 5393, 'min' => 1, 'max' => 539328];
            case 2:return ['valor' => 4223, 'min' => 539329, 'max' => 787746];
            case 3:return ['valor' => 1375, 'min' => 787747, 'max' => 1228614];
            default:return ['valor' => 5393, 'min' => 1, 'max' => 1228614];
        }
    }

    /**
     * Create an operation for a family allowance bracket.
     *
     * @param int $i
     * @param int $schoolId
     * @param float $minLimit
     * @param float $maxLimit
     * @return void
     */
    private function createAsignacionOperation(int $i, int $schoolId, float $minLimit, float $maxLimit)
    {
        foreach ([1, 2] as $workerType) {
            Operation::factory()->create([
                'tuition_id'  => "FILTROASIGFAMT$i",
                'operation'   => 'RENTAIMPONIBLE',
                'worker_type' => $workerType,
                'min_limit'   => $minLimit,
                'max_limit'   => $maxLimit,
                'application' => 111111111111,
                'school_id'   => $schoolId,
            ]);
        }

        Tuition::factory()->create([
            'tuition_id' => "FILTROASIGFAMT$i",
            'title'      => "FILTROASIGFAMT$i",
            'type'       => 'O',
            'school_id'  => $schoolId,
        ]);
    }

    /**
     * Insert costs for a school.
     *
     * @param int $schoolId
     * @return void
     */
    private function insertCostos(int $schoolId)
    {
        $costos = [
            'COSTODIALICENCIANODOCENTE',
            'COSTOHORALICENCIADOCENTE',
            'COSTOHORAINASISTENCIADOCENTE',
            'COSTOHORAINASISTENCIANODOCENTE',
        ];

        foreach ($costos as $costo) {
            Parameter::factory()->create([
                'name'      => $costo,
                'school_id' => $schoolId,
                'value'     => 0,
            ]);
        }
    }

    /**
     * Insert tuitions for a school.
     *
     * @param int $schoolId
     * @return void
     */
    private function insertTuitions(int $schoolId)
    {
        $tuitions = config('school_data.tuitions');

        foreach ($tuitions as $tuition) {
            Tuition::create([
                'tuition_id'     => $tuition[0],
                'title'          => $tuition[1],
                'type'           => $tuition[2],
                'in_liquidation' => $tuition[3],
                'editable'       => $tuition[4],
                'school_id'      => $schoolId,
            ]);
        }
    }

    /**
     * Insert operations for a school.
     *
     * @param int $schoolId
     * @return void
     */
    private function insertOperations(int $schoolId)
    {
        $operations = config('school_data.operations');

        foreach ($operations as $operation) {
            Operation::create([
                'tuition_id'  => $operation[0],
                'worker_type' => $operation[1],
                'operation'   => $operation[2],
                'limit_unit'  => $operation[3],
                'min_limit'   => $operation[4],
                'max_limit'   => $operation[5],
                'max_value'   => $operation[6],
                'application' => $operation[7],
                'school_id'   => $schoolId,
            ]);
        }
    }

    /**
     * Insert bonuses for a school.
     *
     * @param int $schoolId
     * @return void
     */
    private function insertBonuses(int $schoolId)
    {
        // Get bonuses from configuration
        $bonuses = config('school_data.bonuses');
        // Loop through each bonus
        foreach ($bonuses as $bonusData) {
            // Add the school_id to the bonus
            $bonusData['school_id'] = $schoolId;
            // Generate dynamic months if the months field is null
            if ($bonusData['months'] === null) {
                $bonusData['months'] = $this->generateDynamicMonths();
            }
            // Insert the bonus
            Bonus::processCreateBonuses($bonusData);
        }
    }

    /**
     * Insert templates for a school.
     *
     * @param int $schoolId
     * @return void
     */
    private function insertTemplates(int $schoolId)
    {
        // Get templates from configuration
        $templates = config('school_data.templates');

        foreach ($templates as $workerType => $templateData) {
            foreach ($templateData as $index => $template) {
                // Get the tuition_id dynamically if necessary
                $tuitionId = ($template[1] !== '' && $template[1] !== null && $template[0] != 'TEX')
                ? $this->getTuitionIdByTitle($template[1], $schoolId) // Reuse the method
                 : $template[1];

                // Insert the template
                Template::create([
                    'school_id'   => $schoolId,
                    'type'        => $workerType,
                    'position'    => $index + 1,
                    'code'        => $template[0],
                    'tuition_id'  => $tuitionId,
                    'ignore_zero' => $template[2],
                    'parentheses' => $template[3],
                ]);
            }
        }
    }

    /**
     * Generate dynamic months.
     *
     * @param array|null $selectedMonths
     * @return array
     */
    private function generateDynamicMonths($selectedMonths = null)
    {
        if ($selectedMonths === null) {
            $selectedMonths = range(2, 11); // Generate an array of [2, 3, 4, ..., 11]
        }

        $selectedMonths = array_map('strval', $selectedMonths);

        return $selectedMonths;
    }

    /**
     * Generate operations related to laws for a school.
     *
     * @param int $schoolId
     * @return void
     */
    private function generateOperationsLeys(int $schoolId)
    {
        // Get tuition IDs for the required titles
        $tuitionLey19410Id    = $this->getTuitionIdByTitle('Valor Ley 19410', $schoolId);
        $tuitionLey19933Id    = $this->getTuitionIdByTitle('Valor Ley 19933', $schoolId);
        $AplicacionLey19933Id = $this->getTuitionIdByTitle('Aplicación de Ley 19933', $schoolId);
        $AplicacionLey19410Id = $this->getTuitionIdByTitle('Aplicación de Ley 19410', $schoolId);
        $tuitionUmp           = $this->getTuitionIdByTitle('Valor UMP', $schoolId);
        $factorRBMN           = $this->getTuitionIdByTitle('Valor RBMN', $schoolId);
        // Define the operations to be created
        $operations = [
            [
                'tuition_id'  => 'EXCEDENTEBONOSAELEY19410Y19933',
                'worker_type' => Worker::WORKER_TYPE_TEACHER,
                'operation'   => "$tuitionLey19410Id + $tuitionLey19933Id / 0.8 * 12 * 0.2 * CARGAHORARIA / SUMACARGAS",
                'min_limit'   => 0,
                'max_limit'   => 0,
                'max_value'   => 0,
                'application' => '000000000000',
            ],
            [
                'tuition_id'  => 'PLANILLACOMPLEMENTARIA',
                'worker_type' => Worker::WORKER_TYPE_TEACHER,
                'operation'   => "VALORIMD M+ $factorRBMN M- $tuitionUmp M- $tuitionLey19933Id * $AplicacionLey19933Id / SUMACARGAS M- $tuitionLey19410Id * $AplicacionLey19410Id / SUMACARGAS M- MR * CARGAHORARIA * FACTORASIST",
                'min_limit'   => 0,
                'max_limit'   => 0,
                'max_value'   => 0,
                'application' => '111111111111',
            ],
        ];

        // Create the operations
        $this->createOperations($operations, $schoolId);
    }

    /**
     * Create operations in the database.
     *
     * @param array $operations
     * @param int $schoolId
     * @return void
     */
    private function createOperations(array $operations, int $schoolId)
    {
        foreach ($operations as $operation) {
            Operation::create([
                'tuition_id'  => $operation['tuition_id'],
                'worker_type' => $operation['worker_type'],
                'operation'   => $operation['operation'],
                'min_limit'   => $operation['min_limit'],
                'max_limit'   => $operation['max_limit'],
                'max_value'   => $operation['max_value'],
                'application' => $operation['application'],
                'school_id'   => $schoolId,
            ]);
        }
    }

    /**
     * Get the tuition ID for a given title and school ID.
     *
     * @param string $title
     * @param int $schoolId
     * @return string
     */
    private function getTuitionIdByTitle(string $title, int $schoolId): string
    {
        return Tuition::where('title', $title)
            ->where('school_id', $schoolId)
            ->first()
            ->tuition_id;
    }

    /**
     * Delete parameters related to schools.
     *
     * @param array $schoolIds
     * @return void
     */
    public function deleteSchoolParameters(array $schoolIds)
    {
        foreach ($schoolIds as $schoolId) {
            // Delete parameters
            Parameter::where('school_id', $schoolId)->delete();
            // Delete workers
            Worker::where('school_id', $schoolId)->delete();
            // Delete tuitions
            Tuition::where('school_id', $schoolId)->delete();
            // Delete operations
            Operation::where('school_id', $schoolId)->delete();
            // Delete bonuses
            Bonus::where('school_id', $schoolId)->delete();
            // Delete templates
            Template::where('school_id', $schoolId)->delete();
        }
    }
}
