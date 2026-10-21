<?php 

declare(strict_types=1);

use App\ParkingLot\Spot\RegularSpot;
use App\ParkingLot\Ticket\Ticket;
use App\ParkingLot\Vehicle\Car;

require_once __DIR__ . '/../vendor/autoload.php';


$car = new Car('DHK-METRO-GA-4455');
$spot = new RegularSpot(202);
$spot->occupy($car);


// 2 hours later, the car leaves

$entryTime = new DateTimeImmutable('-2 hours -30 minutes'); // 2 hours 30 minutes ago
$ticket = new Ticket('TICKET-123', $car, $spot, $entryTime);


echo "--- Ticket Module Verification ---\n";
echo "Ticket ID: " . $ticket->getTicketId() . "\n";
echo "Vehicle License Plate: " . $ticket->getVehicle()->getLicensePlate() . "\n";
echo "Parking Spot Number: " . $ticket->getParkingSpot()->getSpotNumber() . "\n";



echo "2. Duration (Active): {$ticket->calculateParkingHours()} Hours\n";

$exitTime = new DateTimeImmutable();
$ticket->closeTicket($exitTime);

echo "3. Ticket Closed: " . ($ticket->isClosed() ? 'Yes' : 'No') . "\n";
echo "   Final Billed Hours: {$ticket->calculateParkingHours()} Hours\n";


// invalid exit time test

try {
    $invalidTicket = new Ticket('TCK-FAIL', $car, $spot, new DateTimeImmutable());
    $invalidTicket->closeTicket(new DateTimeImmutable('-1 hour'));
} catch (RuntimeException $e) {
    echo "4. Guard Clause Verified: " . $e->getMessage() . "\n";
}

echo "--- Ticket Module Passed! ---\n";