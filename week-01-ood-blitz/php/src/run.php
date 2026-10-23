<?php 

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\ParkingLot\Manager\ParkingLot;
use App\ParkingLot\Spot\CompactSpot;
use App\ParkingLot\Spot\OversizedSpot;
use App\ParkingLot\Spot\RegularSpot;
use App\ParkingLot\Strategy\BaseFareStrategy;
use App\ParkingLot\Strategy\PeakHoursFareStrategy;
use App\ParkingLot\Vehicle\Car;
use App\ParkingLot\Vehicle\Motorcycle;
use App\ParkingLot\Vehicle\Truck;

echo "==========================================\n";
echo "    PARKING LOT SYSTEM - PURE OOP DEMO    \n";
echo "==========================================\n\n";


$parkingLot = new ParkingLot(new BaseFareStrategy());


$parkingLot->addSpot(new CompactSpot(101));
$parkingLot->addSpot(new RegularSpot(201));
$parkingLot->addSpot(new RegularSpot(202));
$parkingLot->addSpot(new OversizedSpot(301));

echo "[Setup] Added 4 spots (1 Compact, 2 Regular, 1 Oversized)\n\n";


$bike = new Motorcycle('DHK-HA-7788');
$car = new Car('DHK-GA-5566');
$truck = new Truck('CTG-TA-1122');

$bikeTicket = $parkingLot->park($bike);
echo "[Parked] Motorcycle at Spot #{$bikeTicket->getParkingSpot()->getSpotNumber()} | Ticket: {$bikeTicket->getTicketId()}\n";

$carTicket = $parkingLot->park($car);
echo "[Parked] Car at Spot #{$carTicket->getParkingSpot()->getSpotNumber()} | Ticket: {$carTicket->getTicketId()}\n";

$truckTicket = $parkingLot->park($truck);
echo "[Parked] Truck at Spot #{$truckTicket->getParkingSpot()->getSpotNumber()} | Ticket: {$truckTicket->getTicketId()}\n\n";


echo "[Strategy] Switching to Peak Hours Fare Strategy (1.5x Multiplier)\n";
$parkingLot->setFareStrategy(new PeakHoursFareStrategy());


echo "\n--- Processing Checkouts ---\n";

$bikeBill = $parkingLot->processExit($bikeTicket->getTicketId());
echo "[Exit] Motorcycle exited. Total Billed (Peak): \${$bikeBill}\n";

$carBill = $parkingLot->processExit($carTicket->getTicketId());
echo "[Exit] Car exited. Total Billed (Peak): \${$carBill}\n";

$truckBill = $parkingLot->processExit($truckTicket->getTicketId());
echo "[Exit] Truck exited. Total Billed (Peak): \${$truckBill}\n\n";

echo "==========================================\n";
echo "   ALL MODULES INTEGRATED SUCCESSFULLY!   \n";
echo "==========================================\n";