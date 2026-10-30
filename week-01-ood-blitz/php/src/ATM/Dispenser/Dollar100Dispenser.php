<?php

declare(strict_types=1);

namespace App\ATM\Dispenser;

class Dollar100Dispenser extends AbstractCashDispenser
{
    public function __construct(int $availableNotes)
    {
        parent::__construct(100, $availableNotes);
    }
}