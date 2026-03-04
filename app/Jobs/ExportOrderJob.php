<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Throwable;

class ExportOrderJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $orderId) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tracker = OrderExport::query()->firstOrCreate(
            ['order_id' => $this->orderId],
            ['status' => 'queued']
        );

        $tracker->forceFill([
            'status' => 'processing',
            'attempts' => $this->attempts(),
            'last_error' => null,
        ])->save();

        $order = Order::query()
            ->select(['id', 'customer_id', 'status', 'total_amount', 'confirmed_at', 'shipped_at', 'created_at'])
            ->with([
                'customer:id,name,email,phone',
                'items:id,order_id,product_id,quantity,unit_price,total_price',
            ])
            ->findOrFail($this->orderId);

        $response = Http::asJson()
            ->timeout(5)
            ->post((string) config('services.order_export.url'), [
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status->value,
                    'total_amount' => $order->total_amount,
                    'confirmed_at' => optional($order->confirmed_at)->toISOString(),
                    'shipped_at' => optional($order->shipped_at)->toISOString(),
                    'created_at' => optional($order->created_at)->toISOString(),
                ],
                'customer' => [
                    'id' => $order->customer?->id,
                    'name' => $order->customer?->name,
                    'email' => $order->customer?->email,
                    'phone' => $order->customer?->phone,
                ],
                'items' => $order->items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ])->values()->all(),
            ]);

        $response->throw();

        $tracker->forceFill([
            'status' => 'exported',
            'attempts' => $this->attempts(),
            'response_code' => $response->status(),
            'response_body' => mb_substr($response->body(), 0, 4000),
            'last_error' => null,
            'exported_at' => now(),
        ])->save();
    }

    public function failed(?Throwable $exception): void
    {
        OrderExport::query()->updateOrCreate(
            ['order_id' => $this->orderId],
            [
                'status' => 'failed',
                'attempts' => $this->tries,
                'last_error' => $exception?->getMessage(),
            ]
        );
    }
}
