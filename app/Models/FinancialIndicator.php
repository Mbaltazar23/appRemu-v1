<?php

namespace App\Models;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialIndicator extends Model {

    use HasFactory;

    /**
     * Retrieve the list of economic indices.
     * 
     * This method returns an array of available economic indicators that the application can use.
     * It includes their value, label, and the associated permission needed to access the data.
     * 
     * @return array The available economic indices with their respective permissions
     */
    public static function getEconomicIndices() {
        return [
            ['value' => 'uf', 'label' => 'Índices Econ.', 'permission' => 'MANIECO'], // Economic indices
            ['value' => 'impuesto_renta', 'label' => 'Impuesto Renta', 'permission' => 'MANIMREN'], // Income tax
            ['value' => 'correccion_monetaria', 'label' => 'I. Corrección Monetaria', 'permission' => 'MANICOM'], // Monetary correction
            ['value' => 'asignacion_familiar', 'label' => 'Asignación Familiar', 'permission' => 'MANASIGFAM'], // Family allowance
        ];
    }

    /**
     * Retrieve the current values of UF, UTM, Dollar, and Euro from the API.
     * 
     * This method uses an external API (mindicador.cl) to fetch the current values of the UF (Unidad de Fomento), 
     * UTM (Unidad Tributaria Mensual), Dollar, and Euro. It returns the values as an associative array.
     * 
     * @return array|null The current values of the indicators, or null if the request fails
     */
    public static function getCurrentValues() {
        try {
            $client = new Client();  // Guzzle HTTP client to make requests to external API
            // The indicators we need to fetch
            $indicators = ['uf', 'utm', 'dolar', 'euro'];
            // Array to store the current values
            $currentValues = [];
            // Loop through the indicators and fetch their current values
            foreach ($indicators as $indicator) {
                $response = $client->get("https://mindicador.cl/api/{$indicator}");  // API call to fetch the data
                $data = json_decode($response->getBody(), true);  // Decode the JSON response
                // Store the first value from the series (current value)
                $currentValues[$indicator] = isset($data['serie'][0]['valor']) ? $data['serie'][0]['valor'] : null;
            }
            // Return the current values as an associative array
            return $currentValues;
        } catch (\Exception $e) {
            return null; // Return null if the API request fails
        }
    }

    /**
     * Retrieve the values of UF, UTM, Dollar, and Euro for the previous month.
     * 
     * This method fetches the values of UF, UTM, Dollar, and Euro for the previous month using the same external API.
     * The date is calculated dynamically using Carbon, and the API is queried for the values of the previous month.
     * 
     * @param array $currentValues The current values of the indicators, which are passed for context
     * @return array|null The values of the indicators for the previous month, or null if the request fails
     */
    public static function getPreviousMonthValues(array $currentValues) {
        try {
            $client = new Client();  // Guzzle HTTP client to make requests to external API
            $previousValues = [];  // Array to store the previous month's values
            // Loop through the current values to get their previous month's values
            foreach ($currentValues as $indicator => $value) {
                // Calculate the date for the previous month
                $previousMonthDate = Carbon::now()->subMonth()->format('d-m-Y');  // Use Carbon to get the previous month's date
                // Make the API call for the previous month's value
                $response = $client->get("https://mindicador.cl/api/{$indicator}/{$previousMonthDate}");
                $data = json_decode($response->getBody(), true);  // Decode the JSON response
                // Store the previous month's value
                $previousValues[$indicator] = isset($data['serie'][0]['valor']) ? $data['serie'][0]['valor'] : null;
            }
            // Return the previous month's values
            return $previousValues;
        } catch (\Exception $e) {
            return null; // Return null if the API request fails
        }
    }

}
