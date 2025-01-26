<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     * 
     * This method is used to check if the user has permission to make the request.
     * 
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * This method defines the validation rules for the incoming request.
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'name' => 'required', // Name is required
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user)], // Email must be unique, ignoring current user if updating
            'password' => 'required_if:id,null', // Password is required if id is null (when creating a new user)
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')], // Role must exist in the roles table
        ];
    }

}
