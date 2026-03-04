<?php

namespace App\DataTransferObjects;

final readonly class CreateOrderData
{
    public function __construct(
        public int $customerId,
        public array $items,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            customerId: (int) $payload['customer_id'],
            items: array_map(
                static fn (array $item): CreateOrderItemData => CreateOrderItemData::fromArray($item),
                $payload['items'],
            ),
        );
    }
}
