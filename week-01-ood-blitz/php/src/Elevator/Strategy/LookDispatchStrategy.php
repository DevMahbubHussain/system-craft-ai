<?php

declare(strict_types=1);

namespace App\Elevator\Strategy;

use App\Elevator\Car\ElevatorCar;
use App\Elevator\Model\Direction;
use App\Elevator\Model\Request;
use RuntimeException;

class LookDispatchStrategy implements DispatchStrategy
{
    /**
     * @param array<ElevatorCar> $elevators
     */
    public function selectElevator(array $elevators, Request $request): ElevatorCar
    {
        if (empty($elevators)) {
            throw new RuntimeException("No elevators available in the system.");
        }

        $bestCar = null;
        $lowestCost = PHP_INT_MAX;

        foreach ($elevators as $car) {
            $cost = $this->calculateSuitabilityCost($car, $request);

            if ($cost < $lowestCost) {
                $lowestCost = $cost;
                $bestCar = $car;
            }
        }

        if ($bestCar === null) {
            return $elevators[0];
        }

        return $bestCar;
    }

    /**
     * Simple cost function to determine how suitable an elevator is for a given request.
     * Lower cost means more suitable.
     */
    private function calculateSuitabilityCost(ElevatorCar $car, Request $request): int
    {
        $currentFloor = $car->getCurrentFloor();
        $sourceFloor = $request->getSourceFloor();
        $requestDirection = $request->getDirection();
        $carDirection = $car->getDirection();

        $distance = abs($currentFloor - $sourceFloor);

        // 1. Check if list is idel (Idle)
        if ($carDirection === Direction::IDLE) {
            return $distance; // Lowest cost for idle elevators
        }

        // 2. Check if the elevator is moving towards the request floor in the same direction
        $isMovingTowards = ($carDirection === Direction::UP && $currentFloor <= $sourceFloor && $requestDirection === Direction::UP)
            || ($carDirection === Direction::DOWN && $currentFloor >= $sourceFloor && $requestDirection === Direction::DOWN);

        if ($isMovingTowards) {
            return $distance; // Cost based on distance along the route
        }

        // 3. Elevator is moving in the opposite direction or has already passed the request floor (penalty added)
        return $distance + 100;
    }
}