<?php 
declare(strict_types=1);

namespace App\MovieBooking\Order;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case CANCELLED = 'CANCELLED';
    case EXPIRED = 'EXPIRED';
}