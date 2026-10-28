<?php

declare(strict_types=1);

namespace App\Elevator\State;

use App\Elevator\Model\Direction;
use App\Elevator\Car\ElevatorCar;

interface ElevatorState{
    public function handleStep(ElevatorCar $car): void;
    public function handleRequest(ElevatorCar $car, int $floor): void;
    public function getDirection(): Direction;
}