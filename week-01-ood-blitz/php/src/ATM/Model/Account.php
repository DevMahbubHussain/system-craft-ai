<?php

declare(strict_types=1);

namespace App\ATM\Model;

use InvalidArgumentException;
use RuntimeException;

class Account
{
    private array $transactions = [];
    public function __construct(
        private readonly string $accountNumber,
        private readonly string $cardNumber,
        private readonly string $pin,
        private float $balance = 0.0
    ) {
        if ($this->balance < 0) {
            throw new InvalidArgumentException("Initial balance cannot be negative.");
        }
    }

    public function validatePin(string $pin): bool
    {
        return $this->pin === $pin;
    }

    public function debit(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Debit amount must be greater than zero.");
        }

        if ($amount > $this->balance) {
            throw new RuntimeException("Insufficient account balance.");
        }

        $this->balance -= $amount;
    }
    public function credit(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Credit amount must be greater than zero.");
        }

        $this->balance += $amount;
    }

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }
    public function getTransactions(): array
    {
        return $this->transactions;
    }
}
