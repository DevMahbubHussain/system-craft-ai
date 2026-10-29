<?php

declare(strict_types=1);

namespace App\ShoppingCart\Order;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case PAID = 'PAID';
    case SHIPPED = 'SHIPPED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';

    public function canCancel(): bool
    {
        return in_array($this, [self::PENDING, self::PAID], true);
    }
}