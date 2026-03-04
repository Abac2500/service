<?php

use App\Enums\OrderStatus;
use App\Jobs\ExportOrderJob;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('confirming order queues export job', function () {
    Queue::fake();

    $order = Order::factory()
        ->for(Customer::factory())
        ->create([
            'status' => OrderStatus::NEW,
        ]);

    $response = $this->patchJson("/api/v1/orders/$order->id/status", [
        'status' => OrderStatus::CONFIRMED->value,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.status', OrderStatus::CONFIRMED->value);

    Queue::assertPushed(ExportOrderJob::class, function (ExportOrderJob $job) use ($order): bool {
        return $job->orderId === $order->id;
    });
});
