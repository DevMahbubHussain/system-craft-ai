<?php

declare(strict_types=1);

namespace App\ATM\State;

use App\ATM\AtmMachine;
use DomainException;

class IdleState implements AtmState
{
    public function insertCard(AtmMachine $atm, string $cardNumber): void
    {
        $atm->setInsertedCardNumber($cardNumber);
        $atm->changeState(new CardInsertedState());
    }

    public function enterPin(AtmMachine $atm, string $pin): void
    {
        throw new DomainException("Cannot enter PIN. Please insert your card first.");
    }

    public function withdrawCash(AtmMachine $atm, int $amount): array
    {
        throw new DomainException("Cannot withdraw cash. Please insert your card first.");
    }

    public function checkBalance(AtmMachine $atm): float
    {
        throw new DomainException("Cannot check balance. Please insert your card first.");
    }

    public function ejectCard(AtmMachine $atm): void
    {
        throw new DomainException("No card to eject.");
    }
}