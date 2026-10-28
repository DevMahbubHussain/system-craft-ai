<?php

declare(strict_types=1);

namespace App\Elevator\Strategy;

use App\Elevator\Car\ElevatorCar;
use App\Elevator\Model\Request;

interface DispatchStrategy
{
    /**
     * Selects an elevator from the given array of elevators based on the provided request.
     *
     * @param array<ElevatorCar> $elevators
     */
    public function selectElevator(array $elevators, Request $request): ElevatorCar;
}