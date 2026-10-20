<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\ParkingLot\Spot\CompactSpot;
use App\ParkingLot\Spot\OversizedSpot;
use App\ParkingLot\Spot\RegularSpot;
use App\ParkingLot\Vehicle\Car;
use App\ParkingLot\Vehicle\Motorcycle;
use App\ParkingLot\Vehicle\Truck;

$bike = new Motorcycle('DHK-HA-1111');
$car = new Car('DHK-GA-2222');
$truck = new Truck('CTG-TA-3333');

$compactSpot = new CompactSpot(101);
$regularSpot = new RegularSpot(201);
$oversizedSpot = new OversizedSpot(301);

echo "--- ParkingSpot Module Verification ---\n";


$compactSpot->occupy($bike);
echo "1. Spot #{$compactSpot->getSpotNumber()} occupied by " . $compactSpot->getCurrentVehicle()?->getLicensePlate() . " (Success)\n";

try {
    $compactSpot->occupy($bike);
} catch (RuntimeException $e) {
    echo "2. Guard Clause Verified: " . $e->getMessage() . "\n";
}

try {
    $regularSpot->occupy($truck);
} catch (RuntimeException $e) {
    echo "3. Size Validation Verified: " . $e->getMessage() . "\n";
}

$compactSpot->vacate();
echo "4. Spot #{$compactSpot->getSpotNumber()} vacated successfully. Available: " . ($compactSpot->isAvailable() ? 'Yes' : 'No') . "\n";
echo "--- ParkingSpot Module Passed! ---\n";