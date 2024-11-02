<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city' => 'required|string|max:255',
            'levels' => 'nullable|string|max:255',
            'duration' => 'required|string|max:255',
            'total_remuneration' => 'required|numeric',
            'remuneration_gloss' => 'required|string|max:255',
            'origin_city' => 'nullable|string|max:255',
            'schedule' => 'nullable|string|max:255',
        ];
    }
}
