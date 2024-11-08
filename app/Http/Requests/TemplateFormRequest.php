<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateFormRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Debes verificar si el usuario tiene permisos
    }

    /**
     * Obtén las reglas de validación que se aplican a la solicitud.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|integer',
            'school_id' => 'required|integer', // Asegura que el ID de la escuela sea un entero
            'position' => 'required|integer|min:1',
            'code' => 'required|in:N,NV,NVV,N V,_L_,__L,_LL,LLL,TEX',
            'tuition_id' => 'nullable|string|exists:tuitions,tuition_id',
            'ignore_zero' => 'nullable|boolean', // Asegura que sea un valor booleano o nulo
            'parentheses' => 'nullable|boolean', // Puede ser verdadero o falso, es opcional
            'text' => 'nullable|required_if:code,TEX|string|max:255', // Requerido solo si el código es "TEX", debe ser un texto
        ];
    }
}
