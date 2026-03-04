<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

#[ObservedBy([ProductObserver::class])]
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public const LIST_CACHE_VERSION_KEY = 'products:list:version';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'sku',
        'price',
        'stock_quantity',
        'category',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    /**
     * @return HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @param Builder $query
     * @param string|null $category
     * @return Builder
     */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        return $query->when(
            filled($category),
            fn(Builder $builder) => $builder->where('category', $category)
        );
    }

    /**
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when(
            filled($search),
            function (Builder $builder) use ($search): void {
                $term = mb_strtolower((string)$search);

                $builder->where(function (Builder $nested) use ($term): void {
                    $nested->whereRaw('LOWER(name) LIKE ?', ["%$term%"])
                        ->orWhereRaw('LOWER(sku) LIKE ?', ["%$term%"]);
                });
            }
        );
    }

    /**
     * @return int
     */
    public static function listCacheVersion(): int
    {
        $version = Cache::get(self::LIST_CACHE_VERSION_KEY);

        if ($version === null) {
            Cache::forever(self::LIST_CACHE_VERSION_KEY, 1);

            return 1;
        }

        return (int)$version;
    }

    /**
     * @return void
     */
    public static function bumpListCacheVersion(): void
    {
        Cache::forever(
            self::LIST_CACHE_VERSION_KEY,
            self::listCacheVersion() + 1,
        );
    }
}
