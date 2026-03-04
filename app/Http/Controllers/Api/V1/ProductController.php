<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ListProductsRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(ListProductsRequest $request)
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 20);
        $page = (int) ($filters['page'] ?? 1);
        $version = Product::listCacheVersion();

        $cacheKey = 'products:list:v'.$version.':'.md5(json_encode([
            'category' => $filters['category'] ?? null,
            'search' => $filters['search'] ?? null,
            'per_page' => $perPage,
            'page' => $page,
        ]));

        $products = Cache::remember($cacheKey, now()->addSeconds(30), function () use ($filters, $perPage, $page) {
            return Product::query()
                ->select(['id', 'name', 'sku', 'category', 'price', 'stock_quantity'])
                ->category($filters['category'] ?? null)
                ->search($filters['search'] ?? null)
                ->orderBy('id')
                ->paginate($perPage, ['*'], 'page', $page);
        });

        return ProductResource::collection($products);
    }
}
