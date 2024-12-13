<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

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
            'role_id' => 'required|integer|between:1,4',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->input('role_id') == 1) {
            $this->merge(['school_ids' => null]); // Sin escuela asignada
        }
        
        if (!$this->input('password')) {
            // No hacer nada, se mantendrá la contraseña actual
            return;
        }
        // Si se ingresó una nueva contraseña, la encriptamos.
        $this->merge(['password' => Hash::make($this->input('password'))]);
    }
}
