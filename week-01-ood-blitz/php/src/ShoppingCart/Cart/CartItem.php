<?php

declare(strict_types=1);

namespace App\ShoppingCart\Cart;

use App\ShoppingCart\Catalog\CatalogItem;
use InvalidArgumentException;

class CartItem
{
    public function __construct(
        private readonly CatalogItem $item,
        private int $quantity
    ) {
        if ($this->quantity <= 0) {
            throw new InvalidArgumentException("Quantity must be greater than zero.");
        }
    }

    public function getItem(): CatalogItem
    {
        return $this->item;
    }

   public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Quantity must be greater than zero.");
        }

        $this->quantity = $quantity;
    }

    public function increment(int $by = 1): void
    {
        if ($by <= 0) {
            throw new InvalidArgumentException("Increment step must be greater than zero.");
        }

        $this->quantity += $by;
    }

    public function getSubtotal(): float
    {
        return round($this->item->getPrice() * $this->quantity, 2);
    }

    
}