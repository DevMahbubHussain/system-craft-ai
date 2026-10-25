<?php 

declare(strict_types=1);

namespace App\VendingMachine\Inventory;

use InvalidArgumentException;
use RuntimeException;
use App\VendingMachine\Product\Product;

class InventoryManager{

 private array $racks = [];

 public function addRack(Rack $rack): void
    {
        $slotCode = $rack->getSlotCode();

        if (isset($this->racks[$slotCode])) {
            throw new InvalidArgumentException("Rack slot {$slotCode} is already registered.");
        }

        $this->racks[$slotCode] = $rack;
    }

    public function getRack(string $slotCode): Rack
    {
        if (!isset($this->racks[$slotCode])) {
            throw new RuntimeException("Slot code {$slotCode} not found in inventory.");
        }

        return $this->racks[$slotCode];
    }

    public function isAvailable(string $slotCode): bool
    {
        if (!isset($this->racks[$slotCode])) {
            return false;
        }

        return !$this->racks[$slotCode]->isEmpty();
    }

    public function getProduct(string $slotCode): Product
    {
        return $this->getRack($slotCode)->getProduct();
    }

    public function dispenseProduct(string $slotCode): Product
    {
        return $this->getRack($slotCode)->dispense();
    }

    public function isEntirelySoldOut(): bool
    {
        if (empty($this->racks)) {
            return true;
        }

        foreach ($this->racks as $rack) {
            if (!$rack->isEmpty()) {
                return false;
            }
        }

        return true;
    }

    public function getAllRacks(): array
    {
        return $this->racks;
    }

}