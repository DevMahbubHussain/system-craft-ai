<?php 

declare(strict_types=1);

namespace App\ParkingLot\Spot;

use App\ParkingLot\Vehicle\VehicleSize;

class RegularSpot extends ParkingSpot
{
    public function __construct(int $spotNumber)
    {
        parent::__construct($spotNumber, VehicleSize::MEDIUM);
    }
}