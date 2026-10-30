<?php

declare(strict_types=1);

namespace App\ATM\Model;

use RuntimeException;

class Bank{
    private array $accountsByCard = [];
    public function __construct(
        private readonly string $name
    ) {}

    public function registerAccount(Account $account): void
    {
        $this->accountsByCard[$account->getCardNumber()] = $account;
    }
    
    public function authenticate(string $cardNumber, string $pin): Account
    {
        if (!isset($this->accountsByCard[$cardNumber])) {
            throw new RuntimeException("Invalid card: Account not found.");
        }

        $account = $this->accountsByCard[$cardNumber];

        if (!$account->validatePin($pin)) {
            throw new RuntimeException("Authentication failed: Incorrect PIN.");
        }

        return $account;
    }
    public function getName(): string
    {
        return $this->name;
    }
}