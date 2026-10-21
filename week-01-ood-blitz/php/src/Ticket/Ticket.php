<?php 

declare(strict_types=1);

namespace App\ParkingLot\Ticket;

use App\ParkingLot\Spot\ParkingSpot;
use App\ParkingLot\Vehicle\Vehicle;
use DateTimeImmutable;
use RuntimeException;

class Ticket {
    private ?DateTimeImmutable $exitTime = null;
    
    public function __construct(
        private readonly string $ticketId,
        private readonly Vehicle $vehicle,
        private readonly ParkingSpot $parkingSpot,
        private readonly DateTimeImmutable $entryTime = new DateTimeImmutable()
    ) {}

    public function closeTicket(DateTimeImmutable $exitTime): void {
       if($exitTime < $this->entryTime) {
           throw new RuntimeException("Exit time cannot be earlier than entry time.");
       }

       $this->exitTime = $exitTime;
    }
   
    public function calculateParkingHours(): int |float {
        $effectiveExit = $this->exitTime ?? new DateTimeImmutable();
        $durationInSeconds = $effectiveExit->getTimestamp() - $this->entryTime->getTimestamp();
        $hours = ceil($durationInSeconds / 3600);
        return max(1.0,(float)$hours); // Ensure at least 1 hour is counted
    }


    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }

    public function getParkingSpot(): ParkingSpot
    {
        return $this->parkingSpot;
    }

    public function getEntryTime(): DateTimeImmutable
    {
        return $this->entryTime;
    }

    public function getExitTime(): ?DateTimeImmutable
    {
        return $this->exitTime;
    }

    public function isClosed(): bool
    {
        return $this->exitTime !== null;
    }
}