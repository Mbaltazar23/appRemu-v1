<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbsenceFormRequest extends FormRequest
{
    public function authorize()
    {
        // Verifica que el usuario tenga permiso para crear o actualizar ausencias
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'worker_id' => 'required|exists:workers,id', // El trabajador debe existir en la base de datos
            'day' => 'required|integer|min:1|max:31', // Día debe ser un número entre 1 y 31
            'month' => 'required|integer|min:1|max:12', // Mes debe ser un número entre 1 y 12
            'year' => 'required|integer|min:2020|max:2099', // El año debe estar dentro de un rango válido
            'reason' => 'required|string|max:255', // El motivo debe ser una cadena de texto no mayor a 255 caracteres
            'minutes' => 'required|integer|min:1|max:1440', // Los minutos deben estar entre 1 y 1440 (un día)
            'with_consent' => 'nullable|boolean', // Si tiene consentimiento, puede ser un booleano
        ];
    }

    public function prepareForValidation()
    {
       
        if ($this->has('date')) {
            $date = \Carbon\Carbon::parse($this->date);
            $this->merge([
                'day' => $date->day,
                'month' => $date->month,
                'year' => $date->year,
            ]);
        }
    }
}
