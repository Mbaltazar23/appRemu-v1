<?php

namespace App\Models;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            ['value' => 'uf', 'label' => 'Índices Econ.', 'permission' => 'MANIECO'],
            ['value' => 'impuesto_renta', 'label' => 'Impuesto Renta', 'permission' => 'MANIMREN'],
            ['value' => 'correccion_monetaria', 'label' => 'I. Corrección Monetaria', 'permission' => 'MANICOM'],
            ['value' => 'asignacion_familiar', 'label' => 'Asignación Familiar', 'permission' => 'MANASIGFAM'],
        ];
    }
    /**
     * Obtener los valores actuales de UF, UTM, Dólar y Euro desde la API.
     *
     * @return array|null
     */
    public static function getCurrentValues()
    {
        try {
            $client = new Client();
            // Indicadores que necesitamos obtener
            $indicators = ['uf', 'utm', 'dolar', 'euro'];
            // Arreglo donde se guardaran los valores actuales
            $currentValues = [];
            // Hacemos una llamada para obtener los valores actuales
            foreach ($indicators as $indicator) {
                $response = $client->get("https://mindicador.cl/api/{$indicator}");
                $data = json_decode($response->getBody(), true);
                // Almacenamos el primer valor de la serie, que es el valor actual
                $currentValues[$indicator] = isset($data['serie'][0]['valor']) ? $data['serie'][0]['valor'] : null;
            }
            return $currentValues;

        } catch (\Exception $e) {
            return null; // Manejo de errores si la llamada falla
        }
    }
    /**
     * Obtener los valores históricos de UF, UTM, Dólar y Euro para el mes pasado.
     *
     * @param array $currentValues
     * @return array|null
     */
    public static function getPreviousMonthValues(array $currentValues)
    {
        try {
            $client = new Client();
            $previousValues = [];

            foreach ($currentValues as $indicator => $value) {
                // Fecha del mes pasado
                $previousMonthDate = Carbon::now()->subMonth()->format('d-m-Y');
                // Realizamos la llamada para obtener los valores del mes pasado
                $response = $client->get("https://mindicador.cl/api/{$indicator}/{$previousMonthDate}");
                $data = json_decode($response->getBody(), true);
                // Almacenamos el valor del mes pasado
                $previousValues[$indicator] = isset($data['serie'][0]['valor']) ? $data['serie'][0]['valor'] : null;
            }

            return $previousValues;

        } catch (\Exception $e) {
            return null; // Manejo de errores si la llamada falla
        }
    }

}
