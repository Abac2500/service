<?php

namespace App\Enums;

enum OrderStatus: string
{
    case NEW = 'new';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, self::transitions()[$this->value] ?? [], true);
    }

    private static function transitions(): array
    {
        return [
            self::NEW->value => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED->value => [self::PROCESSING, self::CANCELLED],
            self::PROCESSING->value => [self::SHIPPED],
            self::SHIPPED->value => [self::COMPLETED],
            self::COMPLETED->value => [],
            self::CANCELLED->value => [],
        ];
    }
}
