<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Запчасть '.fake()->unique()->numberBetween(1000, 9999),
            'sku' => strtoupper(fake()->unique()->bothify('??###??##')),
            'price' => fake()->randomFloat(2, 20, 4000),
            'stock_quantity' => fake()->numberBetween(10, 500),
            'category' => fake()->randomElement([
                'двигатель',
                'подвеска',
                'тормоза',
                'электрика',
                'кузов',
            ]),
        ];
    }
}
