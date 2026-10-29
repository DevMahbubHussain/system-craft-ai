<?php 

namespace App\ShoppingCart\Order;

use App\ShoppingCart\Cart\Cart;
use App\ShoppingCart\Catalog\Product;
use App\ShoppingCart\Catalog\ProductBundle;
use DateTimeImmutable;
use DomainException;

class Order{
   
    private OrderStatus $status;
    private readonly DateTimeImmutable $createdAt;

    private readonly array $items;
    private readonly float $subtotal;
    private readonly float $total;

    public function __construct(
        private readonly string $orderId,
        private readonly string $userId,
        Cart $cart
    ) {
        if ($cart->getItemCount() === 0) {
            throw new DomainException("Cannot create an order from an empty cart.");
        }

        $this->items = array_values($cart->getItems());
        $this->subtotal = $cart->getSubtotal();
        $this->total = $cart->getTotal();
        $this->status = OrderStatus::PENDING;
        $this->createdAt = new DateTimeImmutable();

        $this->deductInventoryStock();
    }

    public function markAsPaid(): void
    {
        if ($this->status !== OrderStatus::PENDING) {
            throw new DomainException("Only PENDING orders can be marked as PAID.");
        }

        $this->status = OrderStatus::PAID;
    }

    public function ship(): void
    {
        if ($this->status !== OrderStatus::PAID) {
            throw new DomainException("Only PAID orders can be shipped.");
        }

        $this->status = OrderStatus::SHIPPED;
    }

    public function cancel(): void
    {
        if (!$this->status->canCancel()) {
            throw new DomainException("Order cannot be cancelled in state: {$this->status->value}");
        }

        $this->status = OrderStatus::CANCELLED;
        $this->restoreInventoryStock();
    }

    private function deductInventoryStock(): void
    {
        foreach ($this->items as $cartItem) {
            $this->adjustStock($cartItem->getItem(), $cartItem->getQuantity(), deduct: true);
        }
    }
    private function restoreInventoryStock(): void
    {
        foreach ($this->items as $cartItem) {
            $this->adjustStock($cartItem->getItem(), $cartItem->getQuantity(), deduct: false);
        }
    }

    private function adjustStock(object $item, int $quantity, bool $deduct): void
    {
        if ($item instanceof Product) {
            if ($deduct) {
                $item->reduceStock($quantity);
            } else {
                $item->addStock($quantity);
            }
        } elseif ($item instanceof ProductBundle) {
            foreach ($item->getItems() as $subItem) {
                $this->adjustStock($subItem, $quantity, $deduct);
            }
        }
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getItems(): array
    {
        return $this->items;
    }

}