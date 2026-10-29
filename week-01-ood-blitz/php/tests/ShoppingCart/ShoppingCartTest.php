<?php

declare(strict_types=1);

namespace Tests\ShoppingCart;

use App\ShoppingCart\Cart\Cart;
use App\ShoppingCart\Catalog\Product;
use App\ShoppingCart\Catalog\ProductBundle;
use App\ShoppingCart\Discount\FlatDiscountStrategy;
use App\ShoppingCart\Discount\PercentageDiscountStrategy;
use App\ShoppingCart\Order\Order;
use App\ShoppingCart\Order\OrderStatus;
use DomainException;
use PHPUnit\Framework\TestCase;

class ShoppingCartTest extends TestCase
{
    public function testCompositeBundleCalculatesCorrectDiscountedPrice(): void
    {
        $mouse = new Product('P1', 'Mouse', 100.0, 10);
        $keyboard = new Product('P2', 'Keyboard', 200.0, 10);

        // মোট $300, 10% ডিসকাউন্টে বান্ডলের দাম হবে $270
        $bundle = new ProductBundle('B1', 'Combo Pack', 10.0);
        $bundle->addItem($mouse);
        $bundle->addItem($keyboard);

        $this->assertSame(270.0, $bundle->getPrice());
        $this->assertTrue($bundle->isAvailable());
    }

    public function testBundleIsUnavailableIfAnySubItemIsOutOfStock(): void
    {
        $mouse = new Product('P1', 'Mouse', 100.0, 10);
        $keyboard = new Product('P2', 'Keyboard', 200.0, 0); // আউট-অব-স্টক

        $bundle = new ProductBundle('B1', 'Combo Pack', 10.0);
        $bundle->addItem($mouse);
        $bundle->addItem($keyboard);

        $this->assertFalse($bundle->isAvailable());

        $cart = new Cart('CART-1');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("is currently out of stock");

        $cart->addItem($bundle, 1);
    }

    public function testAddingSameProductIncrementsQuantity(): void
    {
        $product = new Product('P1', 'Desk Mat', 30.0, 10);
        $cart = new Cart('CART-1');

        $cart->addItem($product, 2);
        $cart->addItem($product, 3);

        $this->assertSame(5, $cart->getItemCount());
        $this->assertSame(150.0, $cart->getSubtotal());
    }

    public function testDiscountStrategiesCalculation(): void
    {
        $cart = new Cart('CART-1');
        $product = new Product('P1', 'Monitor', 400.0, 5);
        $cart->addItem($product, 1);

        // 1. Percentage Discount with Cap: 20% of 400 is 80, but capped at 50
        $percentDiscount = new PercentageDiscountStrategy(percentage: 20.0, maxDiscountAmount: 50.0);
        $cart->applyDiscount($percentDiscount);
        $this->assertSame(350.0, $cart->getTotal());

        // 2. Flat Discount with Minimum Order Value: $40 off on orders >= $300
        $flatDiscount = new FlatDiscountStrategy(discountAmount: 40.0, minOrderSubtotal: 300.0);
        $cart->applyDiscount($flatDiscount);
        $this->assertSame(360.0, $cart->getTotal());

        // 3. Flat Discount fails if min subtotal not met
        $strictDiscount = new FlatDiscountStrategy(discountAmount: 40.0, minOrderSubtotal: 500.0);
        $cart->applyDiscount($strictDiscount);
        $this->assertSame(400.0, $cart->getTotal());
    }

    public function testCheckoutDeductsInventoryAndCancelRestoresInventory(): void
    {
        $product = new Product('P1', 'Headphones', 150.0, 10);
        $cart = new Cart('CART-1');
        $cart->addItem($product, 2);

        // চেকআউট সম্পাদন
        $order = new Order('ORD-1', 'USER-1', $cart);

        $this->assertSame(OrderStatus::PENDING, $order->getStatus());
        $this->assertSame(8, $product->getStockQuantity()); // স্টক ১০ থেকে কমে ৮ হয়েছে

        // অর্ডার ক্যানসেল
        $order->cancel();

        $this->assertSame(OrderStatus::CANCELLED, $order->getStatus());
        $this->assertSame(10, $product->getStockQuantity()); // স্টক পুনরায় ১০-এ ফিরেছে
    }
}