<?php

declare(strict_types=1);

namespace App\Elevator\State;

use App\Elevator\Car\ElevatorCar;
use App\Elevator\Model\Direction;

class MovingDownState implements ElevatorState
{
    public function handleStep(ElevatorCar $car): void
    {
        // Safety check: ensure the door is closed before moving
        $car->getDoor()->ensureClosed();

        // Move down one floor
        $car->setCurrentFloor($car->getCurrentFloor() - 1);

        $currentFloor = $car->getCurrentFloor();
        $nextFloor = $car->peekNextFloor();

        // If the elevator has reached the requested floor
        if ($nextFloor !== null && $currentFloor === $nextFloor) {
            $car->popNextFloor();
            $car->openDoor();

            // If there are no more requests, transition to IdleState
            if ($car->peekNextFloor() === null) {
                $car->setState(new IdleState());
            }
        }
    }

    public function handleRequest(ElevatorCar $car, int $floor): void
    {
        $car->addFloorToQueue($floor);
    }

    public function getDirection(): Direction
    {
        return Direction::DOWN;
    }
}