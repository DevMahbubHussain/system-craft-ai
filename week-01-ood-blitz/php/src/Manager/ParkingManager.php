<?php 
declare(strict_types=1);

namespace App\ParkingLot\Manager;

use App\ParkingLot\Spot\ParkingSpot;
use App\ParkingLot\Vehicle\Vehicle;
use App\ParkingLot\Vehicle\VehicleSize;
use RuntimeException;

class ParkingManager{

    private array $availableSpots = [];
    private array $allSpots = [];

    public function addSpot(ParkingSpot $spot): void
    {
          $this->allSpots[$spot->getSpotNumber()] = $spot;
          $this->availableSpots[$spot->getSize()->value][$spot->getSpotNumber()] = $spot;

    }

    public function findSpotForVehicle(Vehicle $vehicle): ?ParkingSpot{

        $exactSize = $vehicle->getSize()->value;
        if (!empty($this->availableSpots[$exactSize])) {
            return reset($this->availableSpots[$exactSize]);
        }

        foreach ($this->availableSpots as $spotList) {
            foreach ($spotList as $spot) {
                if ($spot->canFitVehicle($vehicle)) {
                    return $spot;
                }
            }
        }

        return null;
    }

    public function parkVehicle(Vehicle $vehicle): ParkingSpot
    {
        $spot = $this->findSpotForVehicle($vehicle);

        if ($spot === null) {
            throw new RuntimeException("No suitable parking spot available for vehicle: {$vehicle->getLicensePlate()}");
        }

        $spot->occupy($vehicle);

       
        unset($this->availableSpots[$spot->getSize()->value][$spot->getSpotNumber()]);

        return $spot;
    }


    public function unparkVehicle(ParkingSpot $spot): void
    {
        $spot->vacate();
        $this->availableSpots[$spot->getSize()->value][$spot->getSpotNumber()] = $spot;
    }

    public function getAvailableCount(VehicleSize $size): int
    {
        return count($this->availableSpots[$size->value] ?? []);
    }
}