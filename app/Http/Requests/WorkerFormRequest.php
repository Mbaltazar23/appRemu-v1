<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkerFormRequest extends FormRequest {

    public function authorize() {
        return true; // Allows authorization, you can modify it according to your logic
    }

    public function rules() {
        $workerId = $this->route('worker'); // Get the route worker ID, or if it's an update, pass the current ID

        return [
            'insurance_AFP' => 'nullable|integer',
            'insurance_ISAPRE' => 'nullable|integer',
            'school_id' => 'required|exists:schools,id', // Make sure it exists in the schools table
            'rut' => [
                'required',
                'string',
                'max:15',
                Rule::unique('workers')->ignore($workerId), // Ensures that the RUT of the same worker is not validated
            ],
            'name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:100',
            'commune' => 'required|string|max:30',
            'region' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'num_load_family' => 'required|integer|min:0|max:30',
            'marital_status' => 'required|integer',
            'nationality' => 'required|string|max:30',
            'worker_type' => 'required|integer',
            'function_worker' => 'required|integer', // Make sure this field exists in the form
            'contract_type' => 'required|integer',
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date',
            'worker_titular' => 'nullable|exists:workers,id',
            'hourly_load' => 'nullable|numeric|min:1|max:45',
            'unemployment_insurance' => 'required|boolean',
            'retired' => 'required|boolean',
            'service_start_year' => 'nullable|digits:4',
            'base_salary' => 'nullable|numeric',
        ];
    }

}
