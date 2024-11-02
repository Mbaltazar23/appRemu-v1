<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class FinancialIndicator extends Model
{
    use HasFactory;

    /**
     * Obtener el listado de índices económicos.
     *
     * @return array
     */
    public static function getEconomicIndices()
    {
        return [
            ['value' => 'uf', 'label' => 'Índices Econ.'],
            ['value' => 'impuesto_renta', 'label' => 'Impuesto Renta'],
            ['value' => 'correccion_monetaria', 'label' => 'I. Corrección Monetaria'],
            ['value' => 'asignacion_familiar', 'label' => 'Asignación Familiar'],
        ];
    }

   /**
     * Obtener los valores de UF y UTM.
     *
     * @return array|null
     */
    public static function getCurrentValues()
    {
        try {
            $client = new Client();
            $response = $client->get('https://mindicador.cl/api');

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                return [
                    'uf' => $data['uf']['valor'] ?? null,
                    'utm' => $data['utm']['valor'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            return null; // Manejo de errores
        }

        return null; // Manejo de errores
    }
}
