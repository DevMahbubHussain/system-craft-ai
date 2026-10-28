<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Elevator\Car\ElevatorCar;
use App\Elevator\Controller\ElevatorController;
use App\Elevator\Model\Direction;
use App\Elevator\Strategy\LookDispatchStrategy;

echo "====================================================\n";
echo "   ELEVATOR CONTROL SYSTEM - OOD & SIMULATION DEMO   \n";
echo "====================================================\n\n";

// 1. ELEVATOR SYSTEM INITIALIZATION
// Car-1: ID = ELEVATOR-1, Initial Floor = 0
// Car-2: ID = ELEVATOR-2, Initial Floor = 7
$car1 = new ElevatorCar(id: 'ELEVATOR-1', initialFloor: 0);
$car2 = new ElevatorCar(id: 'ELEVATOR-2', initialFloor: 7);

$controller = new ElevatorController(
    elevators: [$car1, $car2],
    strategy: new LookDispatchStrategy()
);

echo "[System Initialized]\n";
echo "• {$car1->getId()} at Floor {$car1->getCurrentFloor()} | State: " . (new ReflectionClass($car1->getState()))->getShortName() . "\n";
echo "• {$car2->getId()} at Floor {$car2->getCurrentFloor()} | State: " . (new ReflectionClass($car2->getState()))->getShortName() . "\n\n";

// ----------------------------------------------------
// SCENARIO 1: Simulation of External Hall Call (External Request)
// ----------------------------------------------------
echo "--- Scenario 1: External Hall Call at Floor 2 (Direction: UP) ---\n";
echo "[User Action] Passenger on Floor 2 presses UP button...\n";

$assignedCar = $controller->handleExternalRequest(sourceFloor: 2, direction: Direction::UP);
echo "Strategy Selected: {$assignedCar->getId()} (Closest Idle Car to Floor 2)\n";
echo "Target Queue for {$assignedCar->getId()}: [" . implode(', ', $assignedCar->getDestinationQueue()) . "]\n\n";

// ----------------------------------------------------
// SCENARIO 2: Simulation of Elevator Moving to Pick Up Passenger
// ----------------------------------------------------
echo "--- Scenario 2: Simulation Running Steps to Pick Up Passenger ---\n";
$stepCount = 1;

while ($assignedCar->getCurrentFloor() !== 2 || !$assignedCar->getDoor()->isOpen()) {
    echo "Step {$stepCount}: {$assignedCar->getId()} moving... ";
    $controller->step();
    echo "Current Floor: {$assignedCar->getCurrentFloor()} | Door: {$assignedCar->getDoor()->getState()->value} | State: " . (new ReflectionClass($assignedCar->getState()))->getShortName() . "\n";
    $stepCount++;

    if ($stepCount > 10) {
        break; // Safety break
    }
}
echo ">>> Passenger picked up at Floor 2! <<<\n\n";

// ----------------------------------------------------
// SCENARIO 3: floor 5 to down (Internal Request)
// ----------------------------------------------------
echo "--- Scenario 3: Passenger Enters {$assignedCar->getId()} and selects Floor 5 ---\n";
echo "[User Action] Inside car, passenger presses '5'...\n";
$controller->handleInternalRequest(elevatorId: $assignedCar->getId(), destinationFloor: 5);

echo "Updated Target Queue: [" . implode(', ', $assignedCar->getDestinationQueue()) . "]\n";

while ($assignedCar->getCurrentFloor() !== 5 || !$assignedCar->getDoor()->isOpen()) {
    echo "Step {$stepCount}: {$assignedCar->getId()} moving... ";
    $controller->step();
    echo "Current Floor: {$assignedCar->getCurrentFloor()} | Door: {$assignedCar->getDoor()->getState()->value} | State: " . (new ReflectionClass($assignedCar->getState()))->getShortName() . "\n";
    $stepCount++;

    if ($stepCount > 20) {
        break; // Safety break
    }
}

echo ">>> Arrived at Floor 5! Passenger dropped off successfully. <<<\n\n";

// if the elevator is still moving or doors are not closed, we can call step() to ensure it completes its cycle
$controller->step();
echo "Post-arrival Status:\n";
echo "• {$assignedCar->getId()} at Floor {$assignedCar->getCurrentFloor()} | Door: {$assignedCar->getDoor()->getState()->value} | State: " . (new ReflectionClass($assignedCar->getState()))->getShortName() . "\n\n";

echo "====================================================\n";
echo "   ELEVATOR SYSTEM SIMULATION COMPLETED!            \n";
echo "====================================================\n";