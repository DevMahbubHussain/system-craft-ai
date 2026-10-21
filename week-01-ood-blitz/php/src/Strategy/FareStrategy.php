<?php 
declare(strict_types=1);

namespace App\ParkingLot\Strategy;

use App\ParkingLot\Ticket\Ticket;

interface FareStrategy {
    public function calculateFare(Ticket $ticket): float;
}