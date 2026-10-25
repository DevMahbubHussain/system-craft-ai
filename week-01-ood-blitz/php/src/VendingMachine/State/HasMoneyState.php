<?php

declare(strict_types=1);

namespace App\VendingMachine\State;
use App\VendingMachine\VendingMachine;

use RuntimeException;

class HasMoneyState implements VendingMachineState{
    public function insertMoney(VendingMachine $machine, float $amount): void
    {
        $machine->getPaymentProcessor()->insertMoney($amount);
    }

    public function selectSlot(VendingMachine $machine, string $slotCode): void
    {
        $inventory = $machine->getInventoryManager();
        if (!$inventory->isAvailable($slotCode)) {
            throw new RuntimeException("Selected slot {$slotCode} is out of stock.");
        }

        $product = $inventory->getProduct($slotCode);
        $payment = $machine->getPaymentProcessor();

        if (!$payment->hasSufficientFunds($product->getPrice())) {
            $shortage = $product->getPrice() - $payment->getCurrentBalance();
            throw new RuntimeException(
                sprintf("Insufficient funds for %s. Price: $%.2f. Need $%.2f more.", 
                $product->getName(), 
                $product->getPrice(), 
                $shortage)
            );
        }

        $machine->setSelectedSlotCode($slotCode);
        $machine->setState(new DispensingState());
    }

    public function dispense(VendingMachine $machine): void
    {
        throw new RuntimeException("Select a valid product slot before dispensing.");
    }

    public function refund(VendingMachine $machine): float
    {
        $refundedAmount = $machine->getPaymentProcessor()->refund();

        $machine->setState(new IdleState());

        return $refundedAmount;
    }
}