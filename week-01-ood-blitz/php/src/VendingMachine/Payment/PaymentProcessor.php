<?php

declare(strict_types=1);

namespace App\VendingMachine\Payment;

use InvalidArgumentException;
use RuntimeException;

class PaymentProcessor
{

private float $currentBalance = 0.0;

 public function insertMoney(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Inserted amount must be greater than zero.");
        }
        $this->currentBalance += $amount;
    }

    public function hasSufficientFunds(float $price): bool
    {
        return $this->currentBalance >= $price;
    }

    public function deduct(float $price): float
    {
        if ($price <= 0.0) {
            throw new InvalidArgumentException("Deduction price must be greater than zero.");
        }

        if (!$this->hasSufficientFunds($price)) {
            throw new RuntimeException("Insufficient funds. Inserted: \${$this->currentBalance}, Required: \${$price}");
        }

        $this->currentBalance -= $price;
        $remainingChange = $this->currentBalance;
        
  
        $this->currentBalance = 0.0;

        return $remainingChange;
    }

    public function refund(): float
    {
        $amountToRefund = $this->currentBalance;
        $this->currentBalance = 0.0;

        return $amountToRefund;
    }

    public function getCurrentBalance(): float
    {
        return $this->currentBalance;
    }
    
}