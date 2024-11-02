<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkerFormRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permite la autorización, puedes modificarlo según tu lógica
    }

    public function rules()
    {
        return [
            'insurance_AFP' => 'nullable|integer',
            'insurance_ISAPRE' => 'nullable|integer',
            'school_id' => 'required|exists:schools,id', // Asegúrate de que exista en la tabla de colegios
            'rut' => 'required|string|max:15',
            'name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:100',
            'commune' => 'required|string|max:30',
            'region' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'marital_status' => 'required|integer', // Cambiado a 'integer'
            'nationality' => 'required|string|max:30',
            'worker_type' => 'required|integer',
            'function_worker' => 'required|integer', // Asegúrate de que este campo exista en el formulario
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date',
            'worker_titular' => 'nullable|exists:workers,id',
            'hourly_load' => 'nullable|numeric|min:1|max:45', // Cambiado a 'numeric'
            'unemployment_insurance' => 'required|boolean', // Validación agregada
            'retired' => 'required|boolean', // Validación agregada
            'service_start_year' => 'nullable|digits:4', // Asegúrate de que sea un año
            'base_salary' => 'nullable|numeric', // Cambiado a 'numeric'
        ];
    }
}
