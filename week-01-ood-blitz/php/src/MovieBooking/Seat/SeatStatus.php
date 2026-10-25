<?php

declare(strict_types=1);

namespace App\MovieBooking\Seat;

enum SeatStatus: string
{
    case AVAILABLE = 'AVAILABLE';
    case HELD = 'HELD';
    case BOOKED = 'BOOKED';
}
