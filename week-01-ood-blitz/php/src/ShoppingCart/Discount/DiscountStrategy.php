<?php

declare(strict_types=1);

namespace App\ShoppingCart\Discount;

interface DiscountStrategy
{

    public function calculateDiscount(float $subtotal): float;

    public function getDescription(): string;
}