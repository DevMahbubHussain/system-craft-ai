<?php

declare(strict_types=1);

namespace App\ParkingLot\Spot;

use App\ParkingLot\Vehicle\Vehicle;
use App\ParkingLot\Vehicle\VehicleSize;
use RuntimeException;

abstract class ParkingSpot
{

    protected ?Vehicle $currentVehicle = null; // current vehicle in spot

    public function __construct(
        protected readonly int $spotNumber, // spot number
        protected readonly VehicleSize $size // spot size
    ) {}

    public function isAvailable(): bool
    {
        return $this->currentVehicle === null;
    }

    public function occupy(Vehicle $vehicle): void
    {
        if (!$this->isAvailable()) {
            throw new RuntimeException("Parking spot is already occupied.");
        }
        if (!$this->canFitVehicle($vehicle)) {
            throw new RuntimeException(
                "Vehicle size {$vehicle->getSize()->value} cannot fit into Spot size {$this->size->value}."
            );
        }

        $this->currentVehicle = $vehicle;
    }

    public function vacate(): void
    {
        if ($this->isAvailable()) {
            throw new RuntimeException("Parking spot is already vacant.");
        }
        $this->currentVehicle = null;
    }

    public function canFitVehicle(Vehicle $vehicle): bool
    {
        return match ($this->size) {
            VehicleSize::SMALL => true,
            VehicleSize::MEDIUM => $vehicle->getSize() !== VehicleSize::LARGE,
            VehicleSize::LARGE => $vehicle->getSize() === VehicleSize::LARGE,
        };
    }

    public function getSpotNumber(): int
    {
        return $this->spotNumber;
    }

    public function getSize(): VehicleSize
    {
        return $this->size;
    }

    public function getCurrentVehicle(): ?Vehicle
    {
        return $this->currentVehicle;
    }
}
