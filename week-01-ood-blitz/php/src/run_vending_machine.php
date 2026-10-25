<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\VendingMachine\Inventory\InventoryManager;
use App\VendingMachine\Inventory\Rack;
use App\VendingMachine\Payment\PaymentProcessor;
use App\VendingMachine\Product\Product;
use App\VendingMachine\VendingMachine;

function money(float $amount): string
{
    return '$' . number_format($amount, 2);
}

echo "====================================================\n";
echo "    VENDING MACHINE SYSTEM - STATE PATTERN DEMO     \n";
echo "====================================================\n\n";

// Creating products and racks for the vending machine
$coke = new Product('PRD-COKE', 'Coca-Cola (330ml)', 1.50);
$chips = new Product('PRD-CHIP', 'Lays Classic Chips', 2.00);

$rackA1 = new Rack('A1', $coke, 2, 5);  
$rackB1 = new Rack('B1', $chips, 1, 5); 

$inventory = new InventoryManager();
$inventory->addRack($rackA1);
$inventory->addRack($rackB1);

$machine = new VendingMachine($inventory, new PaymentProcessor());
echo "[Machine Initialized] Initial State: " . (new ReflectionClass($machine->getState()))->getShortName() . "\n";
echo "Available Racks: A1 (Coke: $1.50, Stock: 2), B1 (Chips: $2.00, Stock: 1)\n\n";

// ----------------------------------------------------
// SCENARIO 1: Successful Purchase of Coca-Cola with Change
// ----------------------------------------------------
echo "--- Scenario 1: Buying Coca-Cola ($1.50) with $2.00 ---\n";
echo "[User Action] Inserting $2.00...\n";
$machine->insertMoney(2.00);
echo "Current Machine State: " . (new ReflectionClass($machine->getState()))->getShortName() . "\n";
echo "Deposited Balance: " . money($machine->getPaymentProcessor()->getCurrentBalance()) . "\n";

echo "[User Action] Selecting slot 'A1'...\n";
$machine->selectSlot('A1');
echo "Current Machine State: " . (new ReflectionClass($machine->getState()))->getShortName() . "\n";

echo "[Machine Action] Dispensing product...\n";
$machine->dispense();
$dispensed = $machine->getLastDispensedProduct();
$change = $machine->getLastReturnedChange();

echo "Dispensed Item: {$dispensed->getName()} (" . money($dispensed->getPrice()) . ")\n";
echo "Returned Change: " . money($change) . "\n";
echo "Remaining Stock in A1: {$rackA1->getQuantity()}\n";
echo "State after Purchase: " . (new ReflectionClass($machine->getState()))->getShortName() . "\n\n";

// ----------------------------------------------------
// SCENARIO 2: Transaction Cancellation and Refund (Rollback)
// ----------------------------------------------------
echo "--- Scenario 2: User inserts money but cancels transaction ---\n";
echo "[User Action] Inserting $5.00...\n";
$machine->insertMoney(5.00);
echo "Deposited Balance: " . money($machine->getPaymentProcessor()->getCurrentBalance()) . "\n";
echo "Current Machine State: " . (new ReflectionClass($machine->getState()))->getShortName() . "\n";

echo "[User Action] Pressed 'Cancel' button...\n";
$refunded = $machine->cancel();
echo "Refund Processed: " . money($refunded) . "\n";
echo "Remaining Balance in Machine: " . money($machine->getPaymentProcessor()->getCurrentBalance()) . "\n";
echo "State after Refund: " . (new ReflectionClass($machine->getState()))->getShortName() . "\n\n";

// ----------------------------------------------------
// SCENARIO 3: Guard Clauses and Invalid Exception Tests
// ----------------------------------------------------
echo "--- Scenario 3: Exception Handling & Rule Protection ---\n";

// Test 3.1: Selecting a product without inserting money
try {
    echo "[Invalid Test 1] Pressing 'A1' in Idle State without inserting money...\n";
    $machine->selectSlot('A1');
} catch (RuntimeException $e) {
    echo "Guard Clause Caught: " . $e->getMessage() . "\n";
}

// Test 3.2: Insufficient funds for the selected product
try {
    echo "[Invalid Test 2] Inserting $1.00 and attempting to buy Chips ($2.00)...\n";
    $machine->insertMoney(1.00);
    $machine->selectSlot('B1');
} catch (RuntimeException $e) {
    echo "Guard Clause Caught: " . $e->getMessage() . "\n";
    // Refund and clear state
    $machine->cancel();
}
echo "\n";

// ----------------------------------------------------
// SCENARIO 4: Stock Depletion and Sold-Out Transition (SoldOutState)
// ----------------------------------------------------
echo "--- Scenario 4: Purchasing Remaining Stock to Trigger SoldOutState ---\n";
echo "Buying remaining Coke from A1...\n";
$machine->insertMoney(1.50);
$machine->selectSlot('A1');
$machine->dispense();
echo "A1 Stock: {$rackA1->getQuantity()}\n";

echo "Buying last remaining product (Chips from B1)...\n";
$machine->insertMoney(2.00);
$machine->selectSlot('B1');
$machine->dispense();
echo "B1 Stock: {$rackB1->getQuantity()}\n";

echo "\n[Inventory Verification] Entirely Sold Out? " . ($inventory->isEntirelySoldOut() ? "YES" : "NO") . "\n";
echo "Current Machine State: " . (new ReflectionClass($machine->getState()))->getShortName() . "\n";

// Test for attempting to insert money into a sold-out machine
try {
    echo "[Sold Out Test] Attempting to insert $1.00 into a Sold Out machine...\n";
    $machine->insertMoney(1.00);
} catch (RuntimeException $e) {
    echo "Guard Clause Caught: " . $e->getMessage() . "\n\n";
}

echo "====================================================\n";
echo "  VENDING MACHINE DOMAIN VERIFIED SUCCESSFULLY!     \n";
echo "====================================================\n";