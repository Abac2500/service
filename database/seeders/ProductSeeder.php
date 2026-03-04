<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['двигатель', 'подвеска', 'тормоза', 'электрика', 'кузов'] as $category) {
            Product::factory()
                ->count(40)
                ->create([
                    'category' => $category,
                ]);
        }
    }
}
