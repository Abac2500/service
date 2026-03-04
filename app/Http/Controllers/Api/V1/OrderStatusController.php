<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UpdateOrderStatusRequest;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use App\Services\OrderService;

class OrderStatusController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function update(
        UpdateOrderStatusRequest $request,
        Order $order,
        OrderService $orderService
    ): OrderResource {
        $nextStatus = OrderStatus::from((string) $request->validated('status'));

        return new OrderResource($orderService->updateStatus($order, $nextStatus));
    }
}
