<?php

namespace Database\Seeders;

use App\Models\Insurance;
use Illuminate\Database\Seeder;

class InsuranceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos para AFP
        $afpData = [
            ['name' => 'CUPRUM', 'cotizacion' => 12.48, 'rut' => '98.001.000-7', 'type' => Insurance::AFP],
            ['name' => 'HABITAT', 'cotizacion' => 12.23, 'rut' => '98.000.100-8', 'type' => Insurance::AFP],
            ['name' => 'PLANVITAL', 'cotizacion' => 12.55, 'rut' => '98.000.900-9', 'type' => Insurance::AFP],
            ['name' => 'PROVIDA', 'cotizacion' => 12.39, 'rut' => '98.000.400-7', 'type' => Insurance::AFP],
            ['name' => 'MODELO', 'cotizacion' => 12.45, 'rut' => '98.000.700-3', 'type' => Insurance::AFP],
            ['name' => 'CAPITAL', 'cotizacion' => 12.50, 'rut' => '98.000.800-1', 'type' => Insurance::AFP],
        ];

        // Datos para ISAPRE
        $isapreData = [
            ['name' => 'BANMÉDICA', 'cotizacion' => 7, 'rut' => '96.572.800-7', 'type' => Insurance::ISAPRE],
            ['name' => 'CONSALUD', 'cotizacion' => 7, 'rut' => '96.856.780-2', 'type' => Insurance::ISAPRE],
            ['name' => 'COLMENA', 'cotizacion' => 7, 'rut' => '94.954.000-6', 'type' => Insurance::ISAPRE],
            ['name' => 'CRUZ BLANCA', 'cotizacion' => 7, 'rut' => '96.999.100-9', 'type' => Insurance::ISAPRE],
            ['name' => 'NUEVA MÁS VIDA', 'cotizacion' => 7, 'rut' => '96.522.500-5', 'type' => Insurance::ISAPRE],
            ['name' => 'VIDA TRES', 'cotizacion' => 7, 'rut' => '96.502.530-8', 'type' => Insurance::ISAPRE],
            ['name' => 'FONASA', 'cotizacion' => 7, 'rut' => '61.603.000-0', 'type' => Insurance::ISAPRE],
        ];

        // Insertar datos en la base de datos
        foreach ($afpData as $data) {
            Insurance::create($data);
        }

        foreach ($isapreData as $data) {
            Insurance::create($data);
        }
    }
}



/*
 // Datos para AFP
        $afpData = [
            ['name' => 'CUPRUM', 'cotizacion' => 12.48, 'rut' => '98.001.000-7', 'type' => Insurance::AFP],
            ['name' => 'HABITAT', 'cotizacion' => 12.23, 'rut' => '98.000.100-8', 'type' => Insurance::AFP],
            ['name' => 'PLANVITAL', 'cotizacion' => 12.55, 'rut' => '98.000.900-9', 'type' => Insurance::AFP],
            ['name' => 'PROVIDA', 'cotizacion' => 12.39, 'rut' => '98.000.400-7', 'type' => Insurance::AFP],
            ['name' => 'SANTA MARIA', 'cotizacion' => 12.42, 'rut' => '98.000.000-1', 'type' => Insurance::AFP],
            ['name' => 'BANSANDER', 'cotizacion' => 12.42, 'rut' => '98.000.600-K', 'type' => Insurance::AFP],
            ['name' => 'IPS', 'cotizacion' => 12, 'rut' => '5.673.467-4', 'type' => Insurance::AFP],
        ];

        // Datos para ISAPRE
        $isapreData = [
            ['name' => 'ALEMANA SALUD S.A.', 'cotizacion' => 7, 'rut' => '96.936.100-0', 'type' => Insurance::ISAPRE],
            ['name' => 'BANMÉDICA S.A.', 'cotizacion' => 7, 'rut' => '96.572.800-7', 'type' => Insurance::ISAPRE],
            ['name' => 'CHUQUICAMATA LIMITADA', 'cotizacion' => 7, 'rut' => '79.566.720-2', 'type' => Insurance::ISAPRE],
            ['name' => 'COLMENA GOLDEN CROSS S.A.', 'cotizacion' => 7, 'rut' => '94.954.000-6', 'type' => Insurance::ISAPRE],
            ['name' => 'CONSALUD S.A.', 'cotizacion' => 7, 'rut' => '96.856.780-2', 'type' => Insurance::ISAPRE],
            ['name' => 'CRUZ DEL NORTE LIMITADA', 'cotizacion' => 7, 'rut' => '79.906.120-1', 'type' => Insurance::ISAPRE],
            ['name' => 'CTC - ISTEL S.A.', 'cotizacion' => 7, 'rut' => '79.727.230-2', 'type' => Insurance::ISAPRE],
            ['name' => 'FERROSALUD S.A.', 'cotizacion' => 7, 'rut' => '96.504.160-5', 'type' => Insurance::ISAPRE],
            ['name' => 'FUNDACIÓN DE SALUD EL TENIENTE', 'cotizacion' => 7, 'rut' => '70.905.700-6', 'type' => Insurance::ISAPRE],
            ['name' => 'F.A.S.T. DEL BANCO DEL ESTADO', 'cotizacion' => 7, 'rut' => '71.235.700-2', 'type' => Insurance::ISAPRE],
            ['name' => 'ING SALUD S.A.', 'cotizacion' => 7, 'rut' => '96.501.450-0', 'type' => Insurance::ISAPRE],
            ['name' => 'MASVIDA S.A.', 'cotizacion' => 7, 'rut' => '96.522.500-5', 'type' => Insurance::ISAPRE],
            ['name' => 'NORMÁDICA S.A.', 'cotizacion' => 7, 'rut' => '95.078.000-2', 'type' => Insurance::ISAPRE],
            ['name' => 'PROMEPART ISAPRE', 'cotizacion' => 7, 'rut' => '81.461.700-9', 'type' => Insurance::ISAPRE],
            ['name' => 'RIO BLANCO LIMITADA', 'cotizacion' => 7, 'rut' => '89.441-300-K', 'type' => Insurance::ISAPRE],
            ['name' => 'SAN LORENZO LIMITADA', 'cotizacion' => 7, 'rut' => '88.497.700-4', 'type' => Insurance::ISAPRE],
            ['name' => 'SFERA S.A.', 'cotizacion' => 7, 'rut' => '96.645.500-4', 'type' => Insurance::ISAPRE],
            ['name' => 'VIDA TRES S.A.', 'cotizacion' => 7, 'rut' => '96.502.530-8', 'type' => Insurance::ISAPRE],
            ['name' => 'FONASA', 'cotizacion' => 7, 'rut' => '61.603.000-0', 'type' => Insurance::ISAPRE],
        ];
*/