<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsuranceFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // Cambia esto según tu lógica de autorización
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'rut' => 'required|string|max:12', // Ajusta según el formato de RUT que uses
            'name' => 'required|string|max:255|unique:insurances,name,',
            'type' => 'required|integer|in:1,2', // 1 para AFP, 2 para Isapre
            'cotizacion' => 'required|regex:/^\d+([.,]\d+)?$/', // Permitir números con decimales
        ];
    }

}
