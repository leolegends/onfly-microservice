<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateTravelRequestRequest extends FormRequest
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
            'requestor_name' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'departure_date' => ['required', 'date', 'after:today'],
            'return_date' => ['required', 'date', 'after:departure_date'],
            'purpose' => ['required', 'string', 'max:1000'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'justification' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos.',
                'errors' => $validator->errors(),
            ], 422)
        );
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
