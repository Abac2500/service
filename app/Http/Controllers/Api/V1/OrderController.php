<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ListOrdersRequest;
use App\Http\Requests\V1\StoreOrderRequest;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(ListOrdersRequest $request)
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 20);

        $orders = Order::query()
            ->select(['id', 'customer_id', 'status', 'total_amount', 'confirmed_at', 'shipped_at', 'created_at'])
            ->status($filters['status'] ?? null)
            ->customer(isset($filters['customer_id']) ? (int) $filters['customer_id'] : null)
            ->createdBetween($filters['date_from'] ?? null, $filters['date_to'] ?? null)
            ->with([
                'customer:id,name,email,phone',
                'items:id,order_id,product_id,quantity,unit_price,total_price',
                'items.product:id,name,sku,price,category,stock_quantity',
            ])
            ->latest('id')
            ->paginate($perPage);

        return OrderResource::collection($orders);
    }

    public function show(Order $order): OrderResource
    {
        $order->load([
            'customer:id,name,email,phone',
            'items:id,order_id,product_id,quantity,unit_price,total_price',
            'items.product:id,name,sku,price,category,stock_quantity',
        ]);

        return new OrderResource($order);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrderRequest $request, OrderService $orderService)
    {
        $order = $orderService->createOrder($request->toDto());

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }
}
