<?php

declare(strict_types=1);

namespace App\Elevator\Model;

enum Direction: string
{
    case UP = 'UP';
    case DOWN = 'DOWN';
    case IDLE = 'IDLE';

    public function isMoving(): bool
    {
        return $this !== self::IDLE;
    }
}
