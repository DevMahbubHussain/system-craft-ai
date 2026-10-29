<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\ShoppingCart\Cart\Cart;
use App\ShoppingCart\Catalog\Product;
use App\ShoppingCart\Catalog\ProductBundle;
use App\ShoppingCart\Discount\PercentageDiscountStrategy;
use App\ShoppingCart\Order\Order;

echo "====================================================\n";
echo "   AMAZON / SHOPPING CART SYSTEM - OOD SIMULATION   \n";
echo "====================================================\n\n";

//Leaf Nodes
$mouse = new Product(id: 'PROD-1', name: 'Logitech MX Master 3S', price: 100.0, stockQuantity: 10);
$keyboard = new Product(id: 'PROD-2', name: 'Keychron Q1 Pro', price: 200.0, stockQuantity: 5);
$deskMat = new Product(id: 'PROD-3', name: 'Wool Felt Desk Mat', price: 30.0, stockQuantity: 20);

// Composite Node
// Bundle
$productivityBundle = new ProductBundle(
    id: 'BUNDLE-1',
    name: 'Pro Productivity Combo (Mouse + Keyboard)',
    discountPercentage: 10.0
);
$productivityBundle->addItem($mouse);
$productivityBundle->addItem($keyboard);

echo "[Catalog Initialized]\n";
echo "• Single Item: {$deskMat->getName()} - \${$deskMat->getPrice()} (Stock: {$deskMat->getStockQuantity()})\n";
echo "• Bundle Item: {$productivityBundle->getName()} - \${$productivityBundle->getPrice()} (Bundle 10% Off)\n\n";

// ----------------------------------------------------
// SCENARIO 1: Add to Cart Composite Pattern Demonstration)
// ----------------------------------------------------
echo "--- Scenario 1: Adding Items to Cart ---\n";
$cart = new Cart(id: 'CART-USER-99');

// Bundel Product
$cart->addItem($deskMat, quantity: 1);
$cart->addItem($productivityBundle, quantity: 1);

echo "Total items in cart: {$cart->getItemCount()}\n";
echo "Cart Subtotal: \${$cart->getSubtotal()}\n\n";

// ----------------------------------------------------
// SCENARIO 2:(Strategy Pattern)
// ----------------------------------------------------
echo "--- Scenario 2: Applying Coupon Strategy ---\n";
// Coupon Strategy
$coupon = new PercentageDiscountStrategy(percentage: 15.0, maxDiscountAmount: 30.0);
$cart->applyDiscount($coupon);

echo "Coupon Applied: {$coupon->getDescription()}\n";
echo "Calculated Discount: \${$coupon->calculateDiscount($cart->getSubtotal())}\n";
echo "Final Cart Total after Discount: \${$cart->getTotal()}\n\n";

// ----------------------------------------------------
// SCENARIO 3: Checkout & Inventory Allocation
// ----------------------------------------------------
echo "--- Scenario 3: Checkout & Inventory Allocation ---\n";
echo "Initial Stocks -> DeskMat: {$deskMat->getStockQuantity()}, Keyboard: {$keyboard->getStockQuantity()}, Mouse: {$mouse->getStockQuantity()}\n";

$order = new Order(orderId: 'ORD-2026-001', userId: 'USER-99', cart: $cart);

echo "Order created successfully! [Order ID: {$order->getOrderId()} | Status: {$order->getStatus()->value}]\n";
echo "Order Total Paid: \${$order->getTotal()}\n";
echo "Post-Checkout Stocks -> DeskMat: {$deskMat->getStockQuantity()}, Keyboard: {$keyboard->getStockQuantity()}, Mouse: {$mouse->getStockQuantity()}\n\n";

// ----------------------------------------------------
// SCENARIO 4: Order Cancel & Restoring Stock
// ----------------------------------------------------
echo "--- Scenario 4: Cancelling Order & Restoring Stock ---\n";
$order->cancel();

echo "Order Status after cancellation: {$order->getStatus()->value}\n";
echo "Restored Stocks -> DeskMat: {$deskMat->getStockQuantity()}, Keyboard: {$keyboard->getStockQuantity()}, Mouse: {$mouse->getStockQuantity()}\n\n";

echo "====================================================\n";
echo "   SHOPPING CART SIMULATION COMPLETED!              \n";
echo "====================================================\n";