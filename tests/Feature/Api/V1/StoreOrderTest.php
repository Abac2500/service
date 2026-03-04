<?php

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates an order, decrements stock and returns calculated totals', function () {
    $customer = Customer::factory()->create();
    $firstProduct = Product::factory()->create([
        'price' => 100.50,
        'stock_quantity' => 10,
    ]);
    $secondProduct = Product::factory()->create([
        'price' => 59.90,
        'stock_quantity' => 20,
    ]);

    $response = $this->postJson('/api/v1/orders', [
        'customer_id' => $customer->id,
        'items' => [
            ['product_id' => $firstProduct->id, 'quantity' => 2],
            ['product_id' => $secondProduct->id, 'quantity' => 3],
        ],
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.status', 'new')
        ->assertJsonPath('data.total_amount', '380.70')
        ->assertJsonPath('data.customer.id', $customer->id)
        ->assertJsonCount(2, 'data.items');

    expect($firstProduct->fresh()->stock_quantity)->toBe(8);
    expect($secondProduct->fresh()->stock_quantity)->toBe(17);

    $this->assertDatabaseHas('orders', [
        'customer_id' => $customer->id,
        'status' => 'new',
        'total_amount' => '380.70',
    ]);
});
