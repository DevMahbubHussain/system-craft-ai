<?php

declare(strict_types=1);

namespace App\ATM\Dispenser;

class Dollar20Dispenser extends AbstractCashDispenser
{
    public function __construct(int $availableNotes)
    {
        parent::__construct(20, $availableNotes);
    }   
}