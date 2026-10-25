<?php

declare(strict_types=1);

namespace App\MovieBooking\Seat;

enum SeatType: string
{
    case REGULAR = 'REGULAR';
    case PREMIUM = 'PREMIUM';
    case VIP = 'VIP';

    public function priceMultiplier(): float
    {
        return match ($this) {
            self::REGULAR => 10.0,
            self::PREMIUM => 15.0,
            self::VIP => 20.0,
        };
    }
}
