<?php 

declare(strict_types=1);

namespace App\ParkingLot\Vehicle;


class Motorcycle implements Vehicle
{
    private string $licensePlate;

    public function __construct(string $licensePlate)
    {
        $this->licensePlate = $licensePlate;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function getSize(): VehicleSize
    {
        return VehicleSize::SMALL;
    }
}