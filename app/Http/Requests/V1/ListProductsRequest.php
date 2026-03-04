<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ListProductsRequest extends FormRequest
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
            'category' => 'nullable|string|max:120',
            'search' => 'nullable|string|max:120',
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
            'category.string' => 'Поле "категория" должно быть строкой.',
            'category.max' => 'Поле "категория" не должно превышать 120 символов.',
            'search.string' => 'Поле "поиск" должно быть строкой.',
            'search.max' => 'Поле "поиск" не должно превышать 120 символов.',
            'per_page.integer' => 'Поле "количество на страницу" должно быть целым числом.',
            'per_page.between' => 'Поле "количество на страницу" должно быть между 1 и 100.',
            'page.integer' => 'Поле "страница" должно быть целым числом.',
            'page.min' => 'Поле "страница" должно быть не меньше 1.',
        ];
    }
}
