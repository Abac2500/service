<?php

namespace App\Http\Requests\V1;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListOrdersRequest extends FormRequest
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
            'status' => ['nullable', Rule::enum(OrderStatus::class)],
            'customer_id' => 'nullable|integer|exists:customers,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'per_page' => 'nullable|integer|between:1,100',
            'page' => 'nullable|integer|min:1',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.enum' => 'Указан недопустимый статус заказа.',
            'customer_id.integer' => 'Поле "клиент" должно быть целым числом.',
            'customer_id.exists' => 'Выбранный клиент не найден.',
            'date_from.date' => 'Поле "дата от" должно быть корректной датой.',
            'date_to.date' => 'Поле "дата до" должно быть корректной датой.',
            'date_to.after_or_equal' => 'Поле "дата до" должно быть больше или равно "дата от".',
            'per_page.integer' => 'Поле "количество на страницу" должно быть целым числом.',
            'per_page.between' => 'Поле "количество на страницу" должно быть между 1 и 100.',
            'page.integer' => 'Поле "страница" должно быть целым числом.',
            'page.min' => 'Поле "страница" должно быть не меньше 1.',
        ];
    }
}
