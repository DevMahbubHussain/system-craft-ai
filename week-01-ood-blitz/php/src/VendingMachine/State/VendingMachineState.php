<?php

declare(strict_types=1);

namespace App\VendingMachine\State;
use App\VendingMachine\VendingMachine;


interface VendingMachineState
{
    public function insertMoney(VendingMachine $machine, float $amount): void;

    public function selectSlot(VendingMachine $machine, string $slotCode): void;

    public function dispense(VendingMachine $machine): void;

    public function refund(VendingMachine $machine): float;
}
