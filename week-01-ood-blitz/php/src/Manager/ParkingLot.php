<?php 
declare(strict_types=1);

namespace App\ParkingLot\Manager;

use App\ParkingLot\Spot\ParkingSpot;
use App\ParkingLot\Strategy\BaseFareStrategy;
use App\ParkingLot\Strategy\FareCalculator;
use App\ParkingLot\Strategy\FareStrategy;
use App\ParkingLot\Ticket\Ticket;
use App\ParkingLot\Vehicle\Vehicle;
use DateTimeImmutable;
use RuntimeException;

class ParkingLot{
   
    private ParkingManager $parkingManager;
    private FareCalculator $fareCalculator;  

    private array $activeTickets = [];

    public function __construct(
        ?FareStrategy $initialStrategy = null
    ) {
        $this->parkingManager = new ParkingManager();
        $this->fareCalculator = new FareCalculator($initialStrategy ?? new BaseFareStrategy());
    }

    public function addSpot(ParkingSpot $spot): void
    {
        $this->parkingManager->addSpot($spot);
    }

    public function setFareStrategy(FareStrategy $strategy): void
    {
        $this->fareCalculator->setStrategy($strategy);
    }

    public function park(Vehicle $vehicle): Ticket
    {
        $spot = $this->parkingManager->parkVehicle($vehicle);

        $ticketId = 'TCK-' . strtoupper(bin2hex(random_bytes(4)));
        $ticket = new Ticket($ticketId, $vehicle, $spot, new DateTimeImmutable());

        $this->activeTickets[$ticketId] = $ticket;

        return $ticket;
    }

    public function processExit(string $ticketId): float
    {
        if (!isset($this->activeTickets[$ticketId])) {
            throw new RuntimeException("Invalid ticket ID: {$ticketId}");
        }

        $ticket = $this->activeTickets[$ticketId];
        $ticket->closeTicket(new DateTimeImmutable());

        $this->parkingManager->unparkVehicle($ticket->getParkingSpot());

        $totalFare = $this->fareCalculator->calculate($ticket);

        unset($this->activeTickets[$ticketId]);

        return $totalFare;
    }

    public function getActiveTicket(string $ticketId): ?Ticket
    {
        return $this->activeTickets[$ticketId] ?? null;
    }

    


}