<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTravelRequestStatusRequest extends FormRequest
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
            'status' => ['required', Rule::in(['approved', 'rejected', 'cancelled'])],
            'reason' => ['required_if:status,rejected,cancelled', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser: approved, rejected ou cancelled.',
            'reason.required_if' => 'O motivo é obrigatório quando o status é rejeitado ou cancelado.',
            'reason.max' => 'O motivo não pode ter mais de 1000 caracteres.',
            'notes.max' => 'As observações não podem ter mais de 1000 caracteres.',
        ];
    }
}
