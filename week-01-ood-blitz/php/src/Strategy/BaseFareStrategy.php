<?php

declare(strict_types=1);

namespace App\ParkingLot\Strategy;

use App\ParkingLot\Ticket\Ticket;
use App\ParkingLot\Vehicle\VehicleSize;

class BaseFareStrategy implements FareStrategy
{

     private const  HOURLY_RATES = [
        VehicleSize::SMALL->value => 10.0,  // Rate for small vehicles 
        VehicleSize::MEDIUM->value => 20.0,  // Rate for medium vehicles
        VehicleSize::LARGE->value => 50.0,   // Rate for large vehicles
    ];



    // Regular Hourly Pricing
    public function calculateFare(Ticket $ticket): float
    {
        $spotsize = $ticket->getParkingSpot()->getSize()->value;
        $rate = self::HOURLY_RATES[$spotsize] ?? 20.0;

        return $ticket->calculateParkingHours() * $rate;
    }

    // Dynamic Surge Pricing
    
}
