<?php

declare(strict_types=1);

namespace App\Elevator\Controller;

use App\Elevator\Car\ElevatorCar;
use App\Elevator\Model\Direction;
use App\Elevator\Model\Request;
use App\Elevator\Strategy\DispatchStrategy;
use App\Elevator\Strategy\LookDispatchStrategy;
use InvalidArgumentException;

class ElevatorController
{
    private array $elevators = [];
    private DispatchStrategy $strategy;

    public function __construct(
        array $elevators = [],
        ?DispatchStrategy $strategy = null
    ) {
        $this->strategy = $strategy ?? new LookDispatchStrategy();

        foreach ($elevators as $car) {
            $this->addElevator($car);
        }
    }

    public function addElevator(ElevatorCar $car): void
    {
        $this->elevators[$car->getId()] = $car;
    }

    public function getElevator(string $id): ?ElevatorCar
    {
        return $this->elevators[$id] ?? null;
    }

    public function getElevators(): array
    {
        return $this->elevators;
    }

    public function handleExternalRequest(int $sourceFloor, Direction $direction): ElevatorCar
    {
        if ($sourceFloor < 0) {
            throw new InvalidArgumentException("Source floor cannot be negative.");
        }

        $request = new Request(
            sourceFloor: $sourceFloor,
            direction: $direction
        );

        // Select the best elevator based on the dispatch strategy
        $bestCar = $this->strategy->selectElevator(array_values($this->elevators), $request);

        // Assign the selected elevator to go to the requested floor
        $bestCar->requestFloor($sourceFloor);

        return $bestCar;
    }

    public function handleInternalRequest(string $elevatorId, int $destinationFloor): void
    {
        $car = $this->getElevator($elevatorId);

        if ($car === null) {
            throw new InvalidArgumentException("Elevator with ID '{$elevatorId}' does not exist.");
        }

        $car->requestFloor($destinationFloor);
    }

    public function step(): void
    {
        foreach ($this->elevators as $car) {
            // If the door is open, close it before the next step
            if ($car->getDoor()->isOpen()) {
                $car->closeDoor();
            }

            $car->step();
        }
    }

    public function setStrategy(DispatchStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }
}