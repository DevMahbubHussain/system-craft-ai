<?php

declare(strict_types=1);

namespace App\VendingMachine\State;

use App\VendingMachine\VendingMachine;
use RuntimeException;

class DispensingState implements VendingMachineState
{
    public function insertMoney(VendingMachine $machine, float $amount): void
    {
        throw new RuntimeException("Cannot insert money while dispensing product.");
    }

    public function selectSlot(VendingMachine $machine, string $slotCode): void
    {
        throw new RuntimeException("Product already selected. Dispensing in progress.");
    }

    public function dispense(VendingMachine $machine): void
    {
        $slotCode = $machine->getSelectedSlotCode();

        if ($slotCode === null) {
            throw new RuntimeException("No product slot selected to dispense.");
        }

        $inventory = $machine->getInventoryManager();
        $payment = $machine->getPaymentProcessor();

        $product = $inventory->getProduct($slotCode);

        $change = $payment->deduct($product->getPrice());

        $dispensedProduct = $inventory->dispenseProduct($slotCode);

        $machine->setLastDispensedProduct($dispensedProduct);
        $machine->setLastReturnedChange($change);

        $machine->setSelectedSlotCode(null);

        if ($inventory->isEntirelySoldOut()) {
            $machine->setState(new SoldOutState());
        } else {
            $machine->setState(new IdleState());
        }
    }

    public function refund(VendingMachine $machine): float
    {
        throw new RuntimeException("Cannot refund while dispensing product.");
    }
}