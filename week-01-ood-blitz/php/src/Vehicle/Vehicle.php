<?php 

declare(strict_types=1);

namespace App\ParkingLot\Vehicle;

interface Vehicle
{
    public function getLicensePlate(): string;
    public function getSize(): VehicleSize;
}
