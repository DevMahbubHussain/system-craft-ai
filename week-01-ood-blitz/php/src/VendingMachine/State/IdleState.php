<?php

declare(strict_types=1);

namespace App\VendingMachine\State;

use App\VendingMachine\VendingMachine;
use RuntimeException;

class IdleState implements VendingMachineState
{

    public function insertMoney(VendingMachine $machine, float $amount): void
    {
        $machine->getPaymentProcessor()->insertMoney($amount);
        $machine->setState(new HasMoneyState());
    }


    public function selectSlot(VendingMachine $machine, string $slotCode): void
    {
        throw new RuntimeException("Please insert money before selecting a product.");
    }

    public function dispense(VendingMachine $machine): void
    {
        throw new RuntimeException("No product selected to dispense.");
    }

    public function refund(VendingMachine $machine): float
    {
        throw new RuntimeException("No money inserted to refund.");
    }
}
