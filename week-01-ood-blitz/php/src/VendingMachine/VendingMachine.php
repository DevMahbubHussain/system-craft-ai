<?php

declare(strict_types=1);

namespace App\VendingMachine;

use App\VendingMachine\Inventory\InventoryManager;
use App\VendingMachine\Payment\PaymentProcessor;
use App\VendingMachine\State\IdleState;
use App\VendingMachine\State\VendingMachineState;
use App\VendingMachine\Product\Product;

class VendingMachine
{

    private VendingMachineState $state;
    private readonly InventoryManager $inventoryManager;
    private readonly PaymentProcessor $paymentProcessor;

    private ?string $selectedSlotCode = null;
    private ?Product $lastDispensedProduct = null;
    private float $lastReturnedChange = 0.0;

    public function __construct(
        ?InventoryManager $inventoryManager = null,
        ?PaymentProcessor $paymentProcessor = null
    ) {
        $this->inventoryManager = $inventoryManager ?? new InventoryManager();
        $this->paymentProcessor = $paymentProcessor ?? new PaymentProcessor();
        $this->state = new IdleState();
    }

    public function setState(VendingMachineState $state): void
    {
        $this->state = $state;
    }

    public function getState(): VendingMachineState
    {
        return $this->state;
    }

    public function insertMoney(float $amount): void
    {
        $this->state->insertMoney($this, $amount);
    }

    public function selectSlot(string $slotCode): void
    {
        $this->state->selectSlot($this, $slotCode);
    }

    public function dispense(): void
    {
        $this->state->dispense($this);
    }

    public function cancel(): float
    {
        return $this->state->refund($this);
    }

    public function getInventoryManager(): InventoryManager
    {
        return $this->inventoryManager;
    }

    public function getPaymentProcessor(): PaymentProcessor
    {
        return $this->paymentProcessor;
    }

    public function getSelectedSlotCode(): ?string
    {
        return $this->selectedSlotCode;
    }

    public function setSelectedSlotCode(?string $slotCode): void
    {
        $this->selectedSlotCode = $slotCode;
    }

    public function getLastDispensedProduct(): ?Product
    {
        return $this->lastDispensedProduct;
    }

    public function setLastDispensedProduct(?Product $product): void
    {
        $this->lastDispensedProduct = $product;
    }

    public function getLastReturnedChange(): float
    {
        return $this->lastReturnedChange;
    }

    public function setLastReturnedChange(float $change): void
    {
        $this->lastReturnedChange = $change;
    }
}
