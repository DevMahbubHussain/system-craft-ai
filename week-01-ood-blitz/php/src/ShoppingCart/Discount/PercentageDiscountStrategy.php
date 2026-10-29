<?php

declare(strict_types=1);

namespace App\ShoppingCart\Discount;

use InvalidArgumentException;

class PercentageDiscountStrategy implements DiscountStrategy
{
 public function __construct(
        private readonly float $percentage,
        private readonly ?float $maxDiscountAmount = null
    ) {
        if ($this->percentage <= 0 || $this->percentage > 100) {
            throw new InvalidArgumentException("Discount percentage must be between 1 and 100.");
        }

        if ($this->maxDiscountAmount !== null && $this->maxDiscountAmount <= 0) {
            throw new InvalidArgumentException("Maximum discount amount must be greater than zero.");
        }
    }



    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        $discount = ($subtotal * $this->percentage) / 100;

        if ($this->maxDiscountAmount !== null && $discount > $this->maxDiscountAmount) {
            $discount = $this->maxDiscountAmount;
        }

        return round($discount, 2);
    }

    public function getDescription(): string
    {
        $desc = "{$this->percentage}% Off";
        if ($this->maxDiscountAmount !== null) {
            $desc .= " (Capped at \${$this->maxDiscountAmount})";
        }
        return $desc;
    }

}