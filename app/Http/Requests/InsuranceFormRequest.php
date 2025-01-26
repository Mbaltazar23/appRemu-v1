<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsuranceFormRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules() {
        return [
            'rut' => 'required|string|max:12', // Adjust according to the RUT format you use
            'name' => 'required|string|max:255|unique:insurances,name,',
            'type' => 'required|integer|in:1,2', // 1 for AFP, 2 for Isapre
            'cotizacion' => 'required|regex:/^\d+([.,]\d+)?$/', // Allow numbers with decimals
        ];
    }

}
