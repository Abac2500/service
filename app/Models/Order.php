<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'status',
        'total_amount',
        'confirmed_at',
        'shipped_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'shipped_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function export(): HasOne
    {
        return $this->hasOne(OrderExport::class);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when(
            filled($status),
            fn (Builder $builder) => $builder->where('status', $status)
        );
    }

    public function scopeCustomer(Builder $query, ?int $customerId): Builder
    {
        return $query->when(
            $customerId !== null,
            fn (Builder $builder) => $builder->where('customer_id', $customerId)
        );
    }

    public function scopeCreatedBetween(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when(
                filled($from),
                fn (Builder $builder) => $builder->where(
                    'created_at',
                    '>=',
                    CarbonImmutable::parse($from)->startOfDay()
                )
            )
            ->when(
                filled($to),
                fn (Builder $builder) => $builder->where(
                    'created_at',
                    '<=',
                    CarbonImmutable::parse($to)->endOfDay()
                )
            );
    }
}
