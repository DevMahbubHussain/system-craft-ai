<?php

declare(strict_types=1);

namespace App\Elevator\Car;

use App\Elevator\Model\Direction;
use App\Elevator\Model\Door;
use App\Elevator\State\ElevatorState;
use App\Elevator\State\IdleState;
use InvalidArgumentException;

class ElevatorCar
{
    private int $currentFloor;
    private ElevatorState $state;
    private readonly Door $door;

    /**
     * Destination queue (Queue/List)
     * @var array<int>
     */
    private array $destinationQueue = [];

    public function __construct(
        private readonly string $id,
        int $initialFloor = 0,
        ?Door $door = null
    ) {
        if ($initialFloor < 0) {
            throw new InvalidArgumentException("Initial floor cannot be negative.");
        }

        $this->currentFloor = $initialFloor;
        $this->door = $door ?? new Door();
        $this->state = new IdleState();
    }

    public function step(): void
    {
        // Delegate to the current state (IdleState / MovingUpState / MovingDownState)
        $this->state->handleStep($this);
    }

    public function requestFloor(int $floor): void
    {
        if ($floor < 0) {
            throw new InvalidArgumentException("Floor cannot be negative.");
        }

        $this->state->handleRequest($this, $floor);
    }

    public function addFloorToQueue(int $floor): void
    {
        if (!in_array($floor, $this->destinationQueue, true)) {
            $this->destinationQueue[] = $floor;
            $this->reorderQueue();
        }
    }

    public function peekNextFloor(): ?int
    {
        return $this->destinationQueue[0] ?? null;
    }

    public function popNextFloor(): ?int
    {
        return array_shift($this->destinationQueue);
    }

    private function reorderQueue(): void
    {
        // Reorder the queue based on the current direction of the elevator
        if ($this->getDirection() === Direction::UP) {
            sort($this->destinationQueue);
        } elseif ($this->getDirection() === Direction::DOWN) {
            rsort($this->destinationQueue);
        }
    }

    // ==========================================
    // Door and Movement Operations
    // ==========================================

    public function openDoor(): void
    {
        $this->door->open();
    }

    public function closeDoor(): void
    {
        $this->door->close();
    }

    // ==========================================
    // Getters and Setters
    // ==========================================

    public function getId(): string
    {
        return $this->id;
    }

    public function getCurrentFloor(): int
    {
        return $this->currentFloor;
    }

    public function setCurrentFloor(int $floor): void
    {
        $this->currentFloor = $floor;
    }

    public function getDirection(): Direction
    {
        return $this->state->getDirection();
    }

    public function getState(): ElevatorState
    {
        return $this->state;
    }

    public function setState(ElevatorState $state): void
    {
        $this->state = $state;
    }

    public function getDoor(): Door
    {
        return $this->door;
    }

    /**
     * @return array<int>
     */
    public function getDestinationQueue(): array
    {
        return $this->destinationQueue;
    }
}