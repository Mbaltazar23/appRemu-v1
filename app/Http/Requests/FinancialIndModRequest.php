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
    
        // Definir una expresión regular que acepte números con punto o coma como separadores decimales
        $numericRule = ['required', 'regex:/^\d+(\,|\.)?\d*$/'];
    
        if ($this->input('index') == 'impuesto_renta') {
            // Si el índice es 'impuesto_renta', hacer IMP y REB requeridos y validarlos
            for ($i = 2; $i <= 8; $i++) {
                $rules["MIN$i"] = $numericRule;
                $rules["MAX$i"] = $numericRule;
                $rules["IMP$i"] = $numericRule; // IMP es requerido
                $rules["REB$i"] = $numericRule; // REB es requerido
            }
        } else {
            // Para los tramos 1 a 3 de asignación familiar
            for ($i = 1; $i <= 3; $i++) {
                $rules["VAL$i"] = $numericRule; // VAL es requerido
                $rules["MIN$i"] = $numericRule; // MIN es requerido
                $rules["MAX$i"] = $numericRule; // MAX es requerido
            }
        }
    
        return $rules;
    }
}
