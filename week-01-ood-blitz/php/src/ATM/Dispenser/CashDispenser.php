<?php

declare(strict_types=1);

namespace App\ATM\Dispenser;

interface CashDispenser
{
    public function setNext(CashDispenser $nextDispenser): CashDispenser;
    public function dispense(int $amount, array &$dispensedNotes): array;
}