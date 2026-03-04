<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ProductObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * @param Product $product
     * @return void
     */
    public function saved(Product $product): void
    {
        Product::bumpListCacheVersion();
    }

    /**
     * @param Product $product
     * @return void
     */
    public function deleted(Product $product): void
    {
        Product::bumpListCacheVersion();
    }
}
