<?php 

declare(strict_types=1);

namespace App\VendingMachine\Inventory;

use App\VendingMachine\Product\Product;
use InvalidArgumentException;
use RuntimeException;

class Rack{
    
public function __construct(
   private readonly string $slotCode,
   private readonly Product $product,
   private int $quantity,
   private readonly int $capacity=10,
    ){
        if ($this->capacity <= 0) {
            throw new InvalidArgumentException("Rack capacity must be greater than zero.");
        }
        if ($this->quantity > $this->capacity) {
            throw new InvalidArgumentException("Quantity exceeds rack capacity ({$this->capacity}).");
        }
    }


    public function isEmpty(): bool{
        return $this->quantity === 0;
    }

    public function dispense(): Product{
        if ($this->isEmpty()) {
            throw new RuntimeException("Rack is empty. Cannot dispense product.");
        }
        $this->quantity--;
        return $this->product;
    }

    public function refill(int $amount): void{
        if ($amount <= 0) {
            throw new InvalidArgumentException("Refill amount must be greater than zero.");
        }
        if ($this->quantity + $amount > $this->capacity) {
            throw new InvalidArgumentException("Refill amount exceeds rack capacity ({$this->capacity}).");
        }
        $this->quantity += $amount;
    }

    public function getSlotCode(): string{
        return $this->slotCode;
    }

    public function getProduct(): Product{
        return $this->product;
    }

    public function getQuantity(): int{
        return $this->quantity;
    }

    public function getCapacity(): int{
        return $this->capacity;
    }
}
