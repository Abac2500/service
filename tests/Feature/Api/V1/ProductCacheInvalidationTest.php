<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('products cache is invalidated after product update', function () {
    $product = Product::factory()->create([
        'name' => 'Тестовый продукт',
        'sku' => 'TESTSKU001',
        'stock_quantity' => 10,
    ]);
    $versionBefore = Product::listCacheVersion();

    $this->getJson('/api/v1/products?search=TESTSKU001')
        ->assertOk()
        ->assertJsonPath('data.0.stock_quantity', 10);

    $product->update(['stock_quantity' => 2]);
    $versionAfter = Product::listCacheVersion();

    $this->getJson('/api/v1/products?search=TESTSKU001')
        ->assertOk()
        ->assertJsonPath('data.0.stock_quantity', 2);

    expect($versionAfter)->toBeGreaterThan($versionBefore);
});
