<?php

namespace App\DataTransferObjects;

final readonly class CreateOrderItemData
{
    public function __construct(
        public int $productId,
        public int $quantity,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            productId: (int) $payload['product_id'],
            quantity: (int) $payload['quantity'],
        );
    }
}
