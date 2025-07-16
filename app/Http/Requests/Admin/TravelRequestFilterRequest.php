<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TravelRequestFilterRequest extends FormRequest
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
            'status' => ['nullable', 'string', 'in:requested,approved,cancelled,rejected'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'destination' => ['nullable', 'string', 'max:255'],
            'departure_date_from' => ['nullable', 'date'],
            'departure_date_to' => ['nullable', 'date', 'after_or_equal:departure_date_from'],
            'request_date_from' => ['nullable', 'date'],
            'request_date_to' => ['nullable', 'date', 'after_or_equal:request_date_from'],
            'order_by' => ['nullable', 'string', 'in:created_at,departure_date,status,destination'],
            'order_direction' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
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
            'status.in' => 'O status deve ser: requested, approved, cancelled ou rejected.',
            'user_id.exists' => 'O usuário especificado não existe.',
            'departure_date_to.after_or_equal' => 'A data final deve ser maior ou igual à data inicial.',
            'request_date_to.after_or_equal' => 'A data final deve ser maior ou igual à data inicial.',
            'order_by.in' => 'A ordenação deve ser: created_at, departure_date, status ou destination.',
            'order_direction.in' => 'A direção da ordenação deve ser: asc ou desc.',
            'per_page.min' => 'O número de itens por página deve ser pelo menos 1.',
            'per_page.max' => 'O número de itens por página não pode ser maior que 100.',
        ];
    }
}
