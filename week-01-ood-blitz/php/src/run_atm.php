<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\ATM\AtmMachine;
use App\ATM\Dispenser\Dollar100Dispenser;
use App\ATM\Dispenser\Dollar50Dispenser;
use App\ATM\Dispenser\Dollar20Dispenser;
use App\ATM\Dispenser\Dollar10Dispenser;
use App\ATM\Model\Account;
use App\ATM\Model\Bank;

echo "====================================================\n";
echo "       ATM SYSTEM - OOD SIMULATION (CHAIN OF RESP)  \n";
echo "====================================================\n\n";

// Bank Initialization
$bank = new Bank("City Bank");
$account = new Account(
    accountNumber: "ACC-100234",
    cardNumber: "4111-2222-3333-4444",
    pin: "1234",
    balance: 1000.0
);
$bank->registerAccount($account);

// Cash Dispenser Chain ($100 -> $50 -> $20 -> $10)
$d100 = new Dollar100Dispenser(availableNotes: 10); // $1000
$d50  = new Dollar50Dispenser(availableNotes: 10);  // $500
$d20  = new Dollar20Dispenser(availableNotes: 10);  // $200
$d10  = new Dollar10Dispenser(availableNotes: 10);  // $100

// Chain of Responsibility
$d100->setNext($d50)->setNext($d20)->setNext($d10);

//  ATM Initialization
$atm = new AtmMachine(atmId: "ATM-DOWNTOWN-01", bank: $bank, dispenserChain: $d100);

echo "[System Initialized]\n";
echo "• User Account: {$account->getAccountNumber()} | Balance: \${$account->getBalance()}\n";
echo "• ATM Vault Configured: $100, $50, $20, $10 Dispensers Active\n\n";

// ----------------------------------------------------
// SCENARIO 1: State Machine Guard Clause
// ----------------------------------------------------
echo "--- Scenario 1: State Machine Guard Clause ---\n";
try {
    $atm->enterPin("1234");
} catch (\DomainException $e) {
    echo "Guard Caught (Expected): {$e->getMessage()}\n\n";
}

// ----------------------------------------------------
// SCENARIO 2: Successful Auth & Cash Dispense
// ----------------------------------------------------
echo "--- Scenario 2: Successful Auth & Cash Dispense ---\n";
$atm->insertCard("4111-2222-3333-4444");
$atm->enterPin("1234");

echo "Card Authenticated. Current Balance: \${$atm->checkBalance()}\n";

$withdrawAmount = 280;
echo "Requesting Withdrawal of: \${$withdrawAmount}\n";

$dispensedNotes = $atm->withdrawCash($withdrawAmount);

echo "Cash Dispensed Successfully:\n";
foreach ($dispensedNotes as $denomination => $count) {
    echo "  - \${$denomination} bills: {$count}\n";
}
echo "New Account Balance: \${$atm->checkBalance()}\n\n";

// ----------------------------------------------------
// SCENARIO 3:session end  & card ejection
// ----------------------------------------------------
echo "--- Scenario 3: Eject Card ---\n";
$atm->ejectCard();
echo "Card ejected. ATM returned to Idle State.\n\n";

// ----------------------------------------------------
// SCENARIO 4: invalid pin
// ----------------------------------------------------
echo "--- Scenario 4: Authentication Failure ---\n";
$atm->insertCard("4111-2222-3333-4444");
try {
    $atm->enterPin("9999");
} catch (\RuntimeException $e) {
    echo "Auth Error Caught: {$e->getMessage()}\n";
}
$atm->ejectCard();

echo "\n====================================================\n";
echo "       ATM SIMULATION COMPLETED!                    \n";
echo "====================================================\n";