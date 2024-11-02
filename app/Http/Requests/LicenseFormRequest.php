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
            'institution' => 'required|string|max:255',
            'receipt_number' => 'required|string|max:255',
            'receipt_date' => 'required|date',
            'processing_date' => 'required|date',
            'responsible_person' => 'required|string|max:255',
        ];
    }

}
