<?php

declare(strict_types=1);

namespace App\Elevator\Model;

use RuntimeException;

enum DoorState: string
{
    case OPEN = 'OPEN';
    case CLOSED = 'CLOSED';
}

class Door {
   private DoorState $state = DoorState::CLOSED;

   public function open(): void
    {
        $this->state = DoorState::OPEN;
    }

    public function close(): void
    {
        $this->state = DoorState::CLOSED;
    }

    public function isOpen(): bool
    {
        return $this->state === DoorState::OPEN;
    }

    public function isClosed(): bool
    {
        return $this->state === DoorState::CLOSED;
    }

    public function ensureClosed(): void
    {
        if ($this->isOpen()) {
            throw new RuntimeException("Safety Hazard: Elevator cannot move while door is open.");
        }
    }

    public function getState(): DoorState
    {
        return $this->state;
    }


}