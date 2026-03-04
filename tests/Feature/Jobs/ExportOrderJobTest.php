<?php

use App\Enums\OrderStatus;
use App\Jobs\ExportOrderJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderExport;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('export order job marks export as exported on successful response', function () {
    config()->set('services.order_export.url', 'https://httpbin.org/post');

    Http::fake([
        'https://httpbin.org/*' => Http::response(['ok' => true], 200),
    ]);

    $customer = Customer::factory()->create();
    $product = Product::factory()->create();

    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'status' => OrderStatus::CONFIRMED,
        'total_amount' => '100.00',
        'confirmed_at' => now(),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => '100.00',
        'total_price' => '100.00',
    ]);

    OrderExport::query()->create([
        'order_id' => $order->id,
        'status' => 'queued',
    ]);

    (new ExportOrderJob($order->id))->handle();

    $this->assertDatabaseHas('order_exports', [
        'order_id' => $order->id,
        'status' => 'exported',
        'response_code' => 200,
    ]);

    Http::assertSentCount(1);
});
