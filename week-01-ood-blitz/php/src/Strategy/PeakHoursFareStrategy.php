<?php

declare(strict_types=1);

namespace App\ParkingLot\Strategy;

use App\ParkingLot\Ticket\Ticket;

class PeakHoursFareStrategy implements FareStrategy
{
  public function __construct(
        private readonly FareStrategy $baseStrategy = new BaseFareStrategy(),
        private readonly float $multiplier = 1.5
    ) {}

    public function calculateFare(Ticket $ticket): float
    {
        $baseAmount = $this->baseStrategy->calculateFare($ticket);
        return $baseAmount * $this->multiplier;
    }
}

