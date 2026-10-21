<?php 

declare(strict_types=1);

namespace App\ParkingLot\Strategy;

use App\ParkingLot\Ticket\Ticket;

class FareCalculator
{
   public function __construct(
       private FareStrategy $strategy
    ) {}

    public function setStrategy(FareStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function calculate(Ticket $ticket): float
    {
        return $this->strategy->calculateFare($ticket);
    }
}