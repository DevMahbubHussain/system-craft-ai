<?php 

declare(strict_types=1);

namespace App\ShoppingCart\Cart;

use App\ShoppingCart\Catalog\CatalogItem;
use App\ShoppingCart\Discount\DiscountStrategy;
use DomainException;
use InvalidArgumentException;

class Cart{
    private array $items = [];

    private ?DiscountStrategy $discountStrategy = null;

    public function __construct(
        private readonly string $id
    ) {}


    public function getId(): string
    {
        return $this->id;
    }

    public function addItem(CatalogItem $item, int $quantity = 1): void
    {
        if (!$item->isAvailable()) {
            throw new DomainException("Item '{$item->getName()}' is currently out of stock.");
        }

        $itemId = $item->getId();

        if (isset($this->items[$itemId])) {
            $this->items[$itemId]->increment($quantity);
        } else {
            $this->items[$itemId] = new CartItem($item, $quantity);
        }
    }

    public function removeItem(string $itemId): void
    {
        unset($this->items[$itemId]);
    }

    public function updateQuantity(string $itemId, int $quantity): void
    {
        if (!isset($this->items[$itemId])) {
            throw new InvalidArgumentException("Item with ID '{$itemId}' not found in cart.");
        }

        if ($quantity <= 0) {
            $this->removeItem($itemId);
            return;
        }

        $this->items[$itemId]->setQuantity($quantity);
    }


    public function getSubtotal(): float
    {
        $subtotal = 0.0;
        foreach ($this->items as $cartItem) {
            $subtotal += $cartItem->getSubtotal();
        }

        return round($subtotal, 2);
    }

    public function applyDiscount(DiscountStrategy $strategy): void
    {
        $this->discountStrategy = $strategy;
    }


    public function getTotal(): float
    {
        $subtotal = $this->getSubtotal();

        if ($this->discountStrategy === null) {
            return $subtotal;
        }

        $discountAmount = $this->discountStrategy->calculateDiscount($subtotal);
        $finalTotal = $subtotal - $discountAmount;

        return round(max(0.0, $finalTotal), 2);
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getItemCount(): int
    {
        $count = 0;
        foreach ($this->items as $cartItem) {
            $count += $cartItem->getQuantity();
        }

        return $count;
    }

    public function clear(): void
    {
        $this->items = [];
        $this->discountStrategy = null;
    }
}
