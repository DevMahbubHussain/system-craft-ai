<?php

declare(strict_types=1);

namespace App\ATM\Model;

use DateTimeImmutable;

enum TransactionType: string
{
    case WITHDRAW = 'WITHDRAW';
    case DEPOSIT = 'DEPOSIT';
    case BALANCE_CHECK = 'BALANCE_CHECK';
}


class Transaction
{
    private readonly DateTimeImmutable $timestamp;

    public function __construct(
        private readonly string $transactionId,
        private readonly string $accountNumber,
        private readonly TransactionType $type,
        private readonly float $amount,
        private readonly float $remainingBalance
    ) {
        $this->timestamp = new DateTimeImmutable();
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getRemainingBalance(): float
    {
        return $this->remainingBalance;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }
}
