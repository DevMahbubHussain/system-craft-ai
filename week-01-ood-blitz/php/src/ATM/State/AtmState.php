<?php

declare(strict_types=1);

namespace App\ATM\State;

use App\ATM\AtmMachine;

interface AtmState
{
    public function insertCard(AtmMachine $atm, string $cardNumber): void;

    public function enterPin(AtmMachine $atm, string $pin): void;

    public function withdrawCash(AtmMachine $atm, int $amount): array;

    public function checkBalance(AtmMachine $atm): float;

    public function ejectCard(AtmMachine $atm): void;
}
