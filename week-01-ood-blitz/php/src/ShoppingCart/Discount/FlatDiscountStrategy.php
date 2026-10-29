<?php

declare(strict_types=1);

namespace App\ShoppingCart\Discount;

use InvalidArgumentException;

class FlatDiscountStrategy implements DiscountStrategy{
    public function __construct(
        private readonly float $discountAmount,
        private readonly float $minOrderSubtotal = 0.0
    ) {
        if ($this->discountAmount <= 0) {
            throw new InvalidArgumentException("Flat discount amount must be greater than zero.");
        }

        if ($this->minOrderSubtotal < 0) {
            throw new InvalidArgumentException("Minimum order subtotal cannot be negative.");
        }
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->minOrderSubtotal) {
            return 0.0;
        }
        return round(min($subtotal, $this->discountAmount), 2);
    }

    public function getDescription(): string
    {
        return "\${$this->discountAmount} Flat Off on orders above \${$this->minOrderSubtotal}";
    }
}