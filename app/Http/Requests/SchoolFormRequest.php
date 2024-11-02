<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolFormRequest extends FormRequest
{
     /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cambia a true si deseas permitir esta solicitud.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rut' => 'required|string|max:12', // Ajusta según el formato esperado
            'name' => 'required|string|max:255',
            'rbd' => 'required|string|max:10', // Ajusta según el formato esperado
            'address' => 'required|string|max:50',
            'commune' => 'required|string|max:30',
            'region' => 'required|string|max:30',
            'director' => 'required|string|max:255',
            'rut_director' => 'required|string|max:12', // Ajusta según el formato esperado
            'phone' => 'required|string|max:15', // Ajusta según el formato esperado
            'email' => 'required|email|max:255',
            'dependency' => 'nullable|in:1,2,3',
            'grantt' => 'nullable|in:1,2,3',
            'sustainer_id' => 'required|exists:sustainers,id', // Asegúrate de que exista en la tabla de sostenedores
        ];
    }
}
