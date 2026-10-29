<?php

declare(strict_types=1);

namespace App\ShoppingCart\Catalog;

use InvalidArgumentException;

class Product implements CatalogItem
{


    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly float $price,
        private int $stockQuantity
    ) {
        if ($this->price < 0) {
            throw new InvalidArgumentException("Product price cannot be negative.");
        }

        if ($this->stockQuantity < 0) {
            throw new InvalidArgumentException("Stock quantity cannot be negative.");
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getStockQuantity(): int
    {
        return $this->stockQuantity;
    }

    public function isAvailable(): bool
    {
        return $this->stockQuantity > 0;
    }

    public function reduceStock(int $quantity): void
    {
        if ($quantity > $this->stockQuantity) {
            throw new InvalidArgumentException("Insufficient stock for product: {$this->name}");
        }

        $this->stockQuantity -= $quantity;
    }

    public function addStock(int $quantity): void
    {
        $this->stockQuantity += $quantity;
    }
}
