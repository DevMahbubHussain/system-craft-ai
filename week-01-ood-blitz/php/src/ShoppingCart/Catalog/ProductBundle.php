<?php

declare(strict_types=1);

namespace App\ShoppingCart\Catalog;

use InvalidArgumentException;


class ProductBundle implements CatalogItem
{
    private array $items = [];

    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly float $discountPercentage = 0.0
    ) {
        if ($this->discountPercentage < 0 || $this->discountPercentage > 100) {
            throw new InvalidArgumentException("Discount percentage must be between 0 and 100.");
        }
    }


    public function addItem(CatalogItem $item): void
    {
        $this->items[$item->getId()] = $item;
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
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item->getPrice();
        }

        if ($this->discountPercentage > 0) {
            $discount = ($total * $this->discountPercentage) / 100;
            return round($total - $discount, 2);
        }

        return round($total, 2);
    }

    public function isAvailable(): bool
    {
        if (empty($this->items)) {
            return false;
        }

        foreach ($this->items as $item) {
            if (!$item->isAvailable()) {
                return false;
            }
        }

        return true;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
