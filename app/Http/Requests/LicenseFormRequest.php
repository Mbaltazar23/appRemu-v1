<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LicenseFormRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Puedes agregar lógica de autorización si es necesario
    }

    public function rules()
    {
        return [
            'worker_id' => 'required|exists:workers,id',
            'issue_date' => 'required|date',
            'reason' => 'required|string|max:255',
            'days' => 'required|integer|min:1',
            'institution' => 'nullable|string|max:255', // Ahora no es obligatorio
            'receipt_number' => 'required|string|max:255',
            'receipt_date' => 'required|date',
            'processing_date' => 'nullable|date', // Ahora no es obligatorio
            'responsible_person' => 'nullable|string|max:255', // Ahora no es obligatorio
        ];
    }
}
