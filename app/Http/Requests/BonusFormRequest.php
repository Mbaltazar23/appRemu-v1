<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BonusFormRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambiar esto si es necesario
    }

    public function rules()
    {
        return [
            'school_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'type' => 'required|integer',
            'is_bonus' => 'required|boolean',
            'taxable' => 'required|boolean',
            'imputable' => 'required|boolean',
            'application' => 'required|string',
            'amount' => 'nullable|numeric', // Permite nulo o valor numérico
            'factor' => 'required|numeric',
            'months.*' => 'integer|between:1,12', // Validar meses
        ];
    }
}
