<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinancialIndModRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia esto según tu lógica de autorización
    }

    public function rules()
    {
        $rules = [];

        for ($i = 2; $i <= 8; $i++) {
            $rules["MIN$i"] = 'required|numeric';
            $rules["MAX$i"] = 'required|numeric';
            $rules["IMP$i"] = 'required|numeric';
            $rules["REB$i"] = 'required|numeric';
        }

        return $rules;
    }
}
