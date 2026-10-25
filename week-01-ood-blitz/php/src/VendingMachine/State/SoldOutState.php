<?php

declare(strict_types=1);

namespace App\VendingMachine\State;
use App\VendingMachine\VendingMachine;
use RuntimeException;

class SoldOutState implements VendingMachineState
{
    public function insertMoney(VendingMachine $machine, float $amount): void
    {
        throw new RuntimeException("Machine is sold out. Cannot accept money.");
    }

    public function selectSlot(VendingMachine $machine, string $slotCode): void
    {
        throw new RuntimeException("Machine is sold out. No products available.");
    }

    public function dispense(VendingMachine $machine): void
    {
        throw new RuntimeException("Machine is sold out. Nothing to dispense.");
    }

    public function refund(VendingMachine $machine): float
    {
        throw new RuntimeException("No transaction in progress to refund.");
    }
}