<?php

declare(strict_types=1);

namespace App\Elevator\State;

use App\Elevator\Car\ElevatorCar;
use App\Elevator\Model\Direction;

class IdleState implements ElevatorState
{
    public function handleStep(ElevatorCar $car): void
    {
        // Next Floor
        $nextFloor = $car->peekNextFloor();

        if ($nextFloor === null) {
            // No request, elevator will remain idle
            return;
        }

        $currentFloor = $car->getCurrentFloor();

        if ($nextFloor > $currentFloor) {
            $car->setState(new MovingUpState());
        } elseif ($nextFloor < $currentFloor) {
            $car->setState(new MovingDownState());
        } else {
            // If the requested floor is the same as the current floor, open the door directly
            $car->popNextFloor();
            $car->openDoor();
        }
    }

    public function handleRequest(ElevatorCar $car, int $floor): void
    {
        $car->addFloorToQueue($floor);

        // Check for immediate transition after receiving a request
        $currentFloor = $car->getCurrentFloor();

        if ($floor > $currentFloor) {
            $car->setState(new MovingUpState());
        } elseif ($floor < $currentFloor) {
            $car->setState(new MovingDownState());
        } else {
            $car->openDoor();
        }
    }

    public function getDirection(): Direction
    {
        return Direction::IDLE;
    }
}