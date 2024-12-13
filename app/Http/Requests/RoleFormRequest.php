<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        // Ajusta esta lógica según sea necesario para autorizar la solicitud
        return true;
    }

    /**
     * Reglas de validación para la solicitud.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $this->route('role')->id,  // Ignorar el rol actual
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:' . implode(',', array_keys(config('permissions'))),
        ];
    }

}
