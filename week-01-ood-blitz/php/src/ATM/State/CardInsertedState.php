<?php

declare(strict_types=1);

namespace App\ATM\State;

use App\ATM\AtmMachine;
use DomainException;

class CardInsertedState implements AtmState
{
    public function insertCard(AtmMachine $atm, string $cardNumber): void
    {
        throw new DomainException("Card already inserted. Please proceed with PIN or cancel.");
    }

    public function enterPin(AtmMachine $atm, string $pin): void
    {
        $cardNumber = $atm->getInsertedCardNumber();
        if ($cardNumber === null) {
            throw new DomainException("No card detected. Please insert your card first.");
        }

        $account = $atm->getBank()->authenticate($cardNumber, $pin);
        $atm->setCurrentAccount($account);
        $atm->changeState(new PinVerifiedState());
    }

    public function withdrawCash(AtmMachine $atm, int $amount): array
    {
        throw new DomainException("Please authenticate with PIN before withdrawing cash.");
    }

    public function checkBalance(AtmMachine $atm): float
    {
        throw new DomainException("Please authenticate with PIN before checking balance.");
    }

    public function ejectCard(AtmMachine $atm): void
    {
        $atm->resetSession();
        $atm->changeState(new IdleState());
    }
}