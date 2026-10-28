<?php

declare(strict_types=1);

namespace App\Elevator\Model;

use InvalidArgumentException;

readonly class Request
{
    public function __construct(
        public readonly int $sourceFloor,
        private ?int $destinationFloor = null,
        private Direction $direction = Direction::IDLE
    ) {
        if ($this->sourceFloor < 0) {
            throw new InvalidArgumentException("Source floor cannot be negative.");
        }

        if ($this->destinationFloor !== null && $this->destinationFloor < 0) {
            throw new InvalidArgumentException("Destination floor cannot be negative.");
        }

        if ($this->destinationFloor !== null && $this->sourceFloor === $this->destinationFloor) {
            throw new InvalidArgumentException("Source and destination floor cannot be identical.");
        }
    }

    public function getSourceFloor(): int
    {
        return $this->sourceFloor;
    }

    public function getDestinationFloor(): ?int
    {
        return $this->destinationFloor;
    }

    public function getDirection(): Direction
    {
        // If the direction is already set, return it. Otherwise, determine the direction based on the source and destination floors.
        if ($this->direction !== Direction::IDLE) {
            return $this->direction;
        }

        if ($this->destinationFloor === null) {
            return Direction::IDLE;
        }

        return $this->destinationFloor > $this->sourceFloor
            ? Direction::UP
            : Direction::DOWN;
    }

    public function isInternal(): bool
    {
        return $this->destinationFloor !== null;
    }
}
