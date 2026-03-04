<?php

namespace App\Http\Requests\V1;

use App\DataTransferObjects\CreateOrderData;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'customer_id' => 'required|integer|exists:customers,id',
            'items' => 'required|array|between:1,100',
            'items.*.product_id' => 'required|integer|distinct|exists:products,id',
            'items.*.quantity' => 'required|integer|between:1,1000000',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Поле "клиент" обязательно для заполнения.',
            'customer_id.integer' => 'Поле "клиент" должно быть целым числом.',
            'customer_id.exists' => 'Выбранный клиент не найден.',
            'items.required' => 'Список товаров обязателен для заполнения.',
            'items.array' => 'Поле "товары" должно быть массивом.',
            'items.between' => 'В заказе должно быть от 1 до 100 позиций.',
            'items.*.product_id.required' => 'Для каждой позиции необходимо указать товар.',
            'items.*.product_id.integer' => 'Идентификатор товара должен быть целым числом.',
            'items.*.product_id.distinct' => 'Товары в заказе не должны повторяться.',
            'items.*.product_id.exists' => 'Один из выбранных товаров не найден.',
            'items.*.quantity.required' => 'Для каждой позиции необходимо указать количество.',
            'items.*.quantity.integer' => 'Количество должно быть целым числом.',
            'items.*.quantity.between' => 'Количество должно быть от 1 до 1000000.',
        ];
    }

    public function toDto(): CreateOrderData
    {
        $validated = $this->validated();

        return CreateOrderData::fromArray($validated);
    }
}
