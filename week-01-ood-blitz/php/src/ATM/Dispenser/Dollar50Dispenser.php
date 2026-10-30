<?php

declare(strict_types=1);

namespace App\ATM\Dispenser;

class Dollar50Dispenser extends AbstractCashDispenser
{
    public function __construct(int $availableNotes)
    {
        parent::__construct(50, $availableNotes);
    }
}