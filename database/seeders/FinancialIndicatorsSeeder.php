<?php
namespace Database\Seeders;

use App\Models\Operation;
use App\Models\Parameter;
use App\Models\SchoolUser;
use App\Models\Tuition;
use Illuminate\Database\Seeder;

class FinancialIndicatorsSeeder extends Seeder
{
    public function run()
    {
        $contadorUser = SchoolUser::first();

        if (!$contadorUser) {
            return; // Si no se encuentra un contador, salimos del método.
        }

        $schoolId    = $contadorUser->school_id;
        $randomMonth = rand(1, 12);
        $cierremes   = ($randomMonth == 2) ? 28 : rand(30, 31); // Asignar el valor de CIERREMES
        // Parámetros comunes para insertar
        $paramsToInsert = [
            'CIERREMES' => $cierremes,
            'VALORIMD'  => rand(1, 1000),
        ];

        $this->insertParameters($paramsToInsert, $schoolId);
        // Insertar valores para el Impuesto
        for ($i = 2; $i <= 8; $i++) {
            $this->insertImpuestoTramo($i, $schoolId);
        }
        // Insertar valores para Asignación Familiar
        for ($i = 1; $i <= 3; $i++) {
            $this->insertAsignacionFamiliar($i, $schoolId);
        }
        // Insertar valores específicos de coste
        $this->insertCostos($schoolId);
    }

    private function insertParameters(array $params, int $schoolId)
    {
        foreach ($params as $name => $value) {
            Parameter::updateOrCreate(
                ['name' => $name, 'school_id' => $schoolId],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    private function insertImpuestoTramo(int $i, int $schoolId)
    {
        $impuestos = $this->getImpuestoValues($i);
        // Crear el parámetro de FACTORIMPTRAMO
        Parameter::factory()->create([
            'name'        => "FACTORIMPTRAMO$i",
            'description' => "Factor Impuesto tramo $i",
            'value'       => $impuestos['impuesto'],
        ]);
        // Crear el parámetro de FACTORREBAJAIMPTRAMO
        Parameter::factory()->create([
            'name'        => "FACTORREBAJAIMPTRAMO$i",
            'description' => "Factor Rebaja Impuesto tramo $i",
            'value'       => $impuestos['rebaja'],
            'unit'        => 'UTM',
        ]);
        // Crear las Tuiciones para los tramos
        $this->createTuition("FACTORIMPTRAMO$i", "Factor Impuesto tramo $i", $schoolId);
        $this->createTuition("FACTORREBAJAIMPTRAMO$i", "Factor Rebaja Impuesto tramo $i", $schoolId);
        // Crear las operaciones para cada tramo
        $this->createOperation($i, $schoolId, $impuestos['min'], $impuestos['max']);
    }

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

    private function createTuition(string $tuitionId, string $title, int $schoolId)
    {
        Tuition::factory()->create([
            'tuition_id' => $tuitionId,
            'title'      => $title,
            'type'       => 'P',
            'school_id'  => $schoolId,
        ]);
    }

    private function createOperation(int $i, int $schoolId, float $minLimit, float $maxLimit)
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

    private function insertAsignacionFamiliar(int $i, int $schoolId)
    {
        $asignacion = $this->getAsignacionValues($i);
        // Crear el parámetro de Asignación Familiar
        Parameter::factory()->create([
            'name'  => "ASIGCAR.FAMTRAMO$i",
            'value' => $asignacion['valor'],
        ]);
        // Crear la tuición
        Tuition::factory()->create([
            'tuition_id' => "ASIGCAR.FAMTRAMO$i",
            'title'      => "Asignacion familiar tramo $i",
            'type'       => 'P',
            'school_id'  => $schoolId,
        ]);
        // Crear las operaciones para Asignación Familiar
        $this->createAsignacionOperation($i, $schoolId, $asignacion['min'], $asignacion['max']);
    }

    private function getAsignacionValues(int $i): array
    {
        switch ($i) {
            case 1:return ['valor' => 5393, 'min' => 1, 'max' => 539328];
            case 2:return ['valor' => 4223, 'min' => 539329, 'max' => 787746];
            case 3:return ['valor' => 1375, 'min' => 787747, 'max' => 1228614];
            default:return ['valor' => 5393, 'min' => 1, 'max' => 1228614];
        }
    }

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
}
