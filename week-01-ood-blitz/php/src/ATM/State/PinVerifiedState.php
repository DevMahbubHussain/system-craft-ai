<?php

declare(strict_types=1);

namespace App\ATM\State;

use App\ATM\AtmMachine;
use App\ATM\Model\Transaction;
use App\ATM\Model\TransactionType;
use DomainException;

class PinVerifiedState implements AtmState
{
    public function insertCard(AtmMachine $atm, string $cardNumber): void
    {
        throw new DomainException("A session is already active.");
    }

    public function enterPin(AtmMachine $atm, string $pin): void
    {
        throw new DomainException("PIN has already been verified.");
    }

    public function checkBalance(AtmMachine $atm): float
    {
        return $atm->getCurrentAccount()->getBalance();
    }

    public function withdrawCash(AtmMachine $atm, int $amount): array
    {
        $account = $atm->getCurrentAccount();

        if ($amount <= 0) {
            throw new DomainException("Withdrawal amount must be greater than zero.");
        }

        $account->debit((float) $amount);

        $dispensedNotes = [];
        try {
            $atm->getDispenserChain()->dispense($amount, $dispensedNotes);
        } catch (\Throwable $e) {
            $account->credit((float) $amount);
            throw $e;
        }

        $tx = new Transaction(
            transactionId: uniqid('TX-', true),
            accountNumber: $account->getAccountNumber(),
            type: TransactionType::WITHDRAW,
            amount: (float) $amount,
            remainingBalance: $account->getBalance()
        );
        $account->addTransaction($tx);

        return $dispensedNotes;
    }

    public function ejectCard(AtmMachine $atm): void
    {
        $atm->resetSession();
        $atm->changeState(new IdleState());
    }
}