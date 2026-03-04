<?php

use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\OrderStatusController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/products', [ProductController::class, 'index']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store'])
        ->middleware('throttle:create-orders');
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderStatusController::class, 'update']);
});
