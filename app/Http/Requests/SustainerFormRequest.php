<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SustainerFormRequest extends FormRequest
{
   /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;  // Aquí puedes ajustar la lógica de autorización si es necesario
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'rut' => 'required|string|max:20',
            'business_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'commune' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'legal_nature' => 'required|string|max:100',
            'legal_representative' => 'required|string|max:255',
            'rut_legal_representative' => 'required|string|max:20',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
        ];
    }
}
