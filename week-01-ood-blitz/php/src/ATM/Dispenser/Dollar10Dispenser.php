<?php

declare(strict_types=1);

namespace App\ATM\Dispenser;

class Dollar10Dispenser extends AbstractCashDispenser
{
    public function __construct(int $availableNotes)
    {
        parent::__construct(10, $availableNotes);
    }
}