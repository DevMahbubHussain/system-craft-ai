<?php 

declare(strict_types=1);

use App\ParkingLot\Vehicle\Car;
use App\ParkingLot\Vehicle\Motorcycle;
use App\ParkingLot\Vehicle\Truck;

require_once __DIR__  . '/../vendor/autoload.php';



$car = new Car('DHK-METRO-GA-1122');
$bike = new Motorcycle('DHK-METRO-HA-5544');
$truck = new Truck('CTG-METRO-TA-9988');


echo "--- Vehicle Module Verification ---\n";
echo "1. Vehicle: " . get_class($car) . " | Plate: " . $car->getLicensePlate() . " | Size: " . $car->getSize()->value . "\n";
echo "2. Vehicle: " . get_class($bike) . " | Plate: " . $bike->getLicensePlate() . " | Size: " . $bike->getSize()->value . "\n";
echo "3. Vehicle: " . get_class($truck) . " | Plate: " . $truck->getLicensePlate() . " | Size: " . $truck->getSize()->value . "\n";

echo "--- All Vehicles Loaded Perfectly! ---\n";