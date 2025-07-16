<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 
                       Rule::unique('users')->ignore($this->user()->id)],
            'department' => ['sometimes', 'nullable', 'string', 'max:255'],
            'position' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está sendo usado por outro usuário.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'department.string' => 'O departamento deve ser um texto.',
            'department.max' => 'O departamento não pode ter mais de 255 caracteres.',
            'position.string' => 'O cargo deve ser um texto.',
            'position.max' => 'O cargo não pode ter mais de 255 caracteres.',
            'phone.string' => 'O telefone deve ser um texto.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
        ];
    }
}
