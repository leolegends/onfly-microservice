<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTravelRequestRequest extends FormRequest
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
            'requestor_name' => ['sometimes', 'required', 'string', 'max:255'],
            'destination' => ['sometimes', 'required', 'string', 'max:255'],
            'departure_date' => ['sometimes', 'required', 'date', 'after:today'],
            'return_date' => ['sometimes', 'required', 'date', 'after:departure_date'],
            'purpose' => ['sometimes', 'required', 'string', 'max:1000'],
            'estimated_cost' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999999.99'],
            'justification' => ['sometimes', 'required', 'string', 'max:2000'],
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
            'requestor_name.required' => 'O nome do solicitante é obrigatório.',
            'destination.required' => 'O destino é obrigatório.',
            'departure_date.required' => 'A data de ida é obrigatória.',
            'departure_date.after' => 'A data de ida deve ser posterior a hoje.',
            'return_date.required' => 'A data de volta é obrigatória.',
            'return_date.after' => 'A data de volta deve ser posterior à data de ida.',
            'purpose.required' => 'O propósito da viagem é obrigatório.',
            'purpose.max' => 'O propósito não pode ter mais de 1000 caracteres.',
            'estimated_cost.numeric' => 'O custo estimado deve ser um número.',
            'estimated_cost.min' => 'O custo estimado não pode ser negativo.',
            'justification.required' => 'A justificativa é obrigatória.',
            'justification.max' => 'A justificativa não pode ter mais de 2000 caracteres.',
        ];
    }
}
