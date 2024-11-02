<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user)],
            'password' => 'required_if:id,null',
            'role' => 'required|integer|between:1,4',
            'school_id' => $this->getSchoolIdsRules(),
            'permissions' => 'array', // Permitir un array de permisos
        ];
    }

    protected function getSchoolIdsRules()
    {
        $rules = ['array']; // Asegurarse de que sea un array

        if ($this->input('role') > 1) { // Solo aplicamos reglas si el rol es mayor que 1
            $user = User::find(auth()->user()->id);
            if ($user->isSuperAdmin()) {
                // Verificar que las escuelas sean parte de las que el admin puede gestionar
                $rules[] = Rule::exists('schools', 'id')->where(function ($query) use ($user) {
                    $query->whereIn('id', $user->schools()->pluck('school_id'));
                });
            } else {
                // Para otros roles, solo verificar existencia
                $rules[] = 'exists:schools,id';
            }
        } else {
            // Si el rol es Super Admin o no necesita escuela, se permite que sea nullable
            $rules[] = 'nullable';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->input('role') == 1) {
            $this->merge(['school_ids' => null]); // Sin escuela asignada
        }

        // Generar una contraseña aleatoria si no se ingresó
        if (!$this->input('password')) {
            $this->merge(['password' => Hash::make("password")]);
        }
    }
}
