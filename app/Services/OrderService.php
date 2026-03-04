<?php

namespace App\Services;

use App\DataTransferObjects\CreateOrderData;
use App\Enums\OrderStatus;
use App\Events\OrderConfirmed;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * @throws \Throwable
     */
    public function createOrder(CreateOrderData $payload): Order
    {
        return DB::transaction(function () use ($payload): Order {
            $items = $payload->items;
            $productIds = collect($items)->pluck('productId')->all();

            $products = Product::query()
                ->select(['id', 'price', 'stock_quantity'])
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $order = Order::query()->create([
                'customer_id' => $payload->customerId,
                'status' => OrderStatus::NEW,
                'total_amount' => 0,
            ]);

            $orderItems = [];
            $totalAmountCents = 0;

            foreach ($items as $index => $item) {
                $product = $products->get($item->productId);

                if ($product === null) {
                    throw ValidationException::withMessages([
                        "items.$index.product_id" => 'Выбранный товар недоступен.',
                    ]);
                }

                $quantity = $item->quantity;

                if ($product->stock_quantity < $quantity) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => 'Недостаточно остатка по выбранному товару.',
                    ]);
                }

                $unitPriceCents = (int) round(((float) $product->price) * 100);
                $lineTotalCents = $unitPriceCents * $quantity;

                $product->decrement('stock_quantity', $quantity);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => number_format($unitPriceCents / 100, 2, '.', ''),
                    'total_price' => number_format($lineTotalCents / 100, 2, '.', ''),
                ];
                $totalAmountCents += $lineTotalCents;
            }

            $order->items()->createMany($orderItems);
            $order->update([
                'total_amount' => number_format($totalAmountCents / 100, 2, '.', ''),
            ]);

            return $this->loadOrderRelations($order);
        }, 3);
    }

    /**
     * @throws \Throwable
     */
    public function updateStatus(Order $order, OrderStatus $nextStatus): Order
    {
        return DB::transaction(function () use ($order, $nextStatus): Order {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            /** @var OrderStatus $currentStatus */
            $currentStatus = $lockedOrder->status;

            if (! $currentStatus->canTransitionTo($nextStatus)) {
                throw ValidationException::withMessages([
                    'status' => "Недопустимый переход статуса: {$currentStatus->value} -> {$nextStatus->value}.",
                ]);
            }

            $updateData = [
                'status' => $nextStatus->value,
            ];

            if ($nextStatus === OrderStatus::CONFIRMED && $lockedOrder->confirmed_at === null) {
                $updateData['confirmed_at'] = now();
            }

            if ($nextStatus === OrderStatus::SHIPPED && $lockedOrder->shipped_at === null) {
                $updateData['shipped_at'] = now();
            }

            $lockedOrder->update($updateData);

            if ($nextStatus === OrderStatus::CONFIRMED) {
                event(new OrderConfirmed($lockedOrder->id));
            }

            return $this->loadOrderRelations($lockedOrder);
        }, 3);
    }

    private function loadOrderRelations(Order $order): Order
    {
        return $order->load([
            'customer:id,name,email,phone',
            'items:id,order_id,product_id,quantity,unit_price,total_price',
            'items.product:id,name,sku,price,category,stock_quantity',
        ]);
    }
}
