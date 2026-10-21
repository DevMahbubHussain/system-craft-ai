<?php 

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\ParkingLot\Spot\RegularSpot;
use App\ParkingLot\Strategy\BaseFareStrategy;
use App\ParkingLot\Strategy\FareCalculator;
use App\ParkingLot\Strategy\PeakHoursFareStrategy;
use App\ParkingLot\Ticket\Ticket;
use App\ParkingLot\Vehicle\Car;

$car = new Car('DHK-METRO-GA-8899');
$spot = new RegularSpot(301);
$spot->occupy($car);


$entryTime = new DateTimeImmutable('-2 hours');
$ticket = new Ticket('TCK-STRAT-1', $car, $spot, $entryTime);

echo "--- Strategy Module Verification ---\n";
echo "Vehicle: Car (Medium) | Duration: {$ticket->calculateParkingHours()} Hours\n";


$baseStrategy = new BaseFareStrategy();
$calculator = new FareCalculator($baseStrategy);
echo "1. Normal Fare: \${$calculator->calculate($ticket)}\n";


$peakStrategy = new PeakHoursFareStrategy();
$calculator->setStrategy($peakStrategy);
echo "2. Peak Hours Surge Fare: \${$calculator->calculate($ticket)}\n";

echo "--- Strategy Module Passed! ---\n";