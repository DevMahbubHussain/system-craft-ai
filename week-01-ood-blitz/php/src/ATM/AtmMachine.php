<?php

declare(strict_types=1);

namespace App\ATM;

use App\ATM\Dispenser\CashDispenser;
use App\ATM\Model\Account;
use App\ATM\Model\Bank;
use App\ATM\State\AtmState;
use App\ATM\State\IdleState;

class AtmMachine
{
    private AtmState $state;
    private ?string $insertedCardNumber = null;
    private ?Account $currentAccount = null;

    public function __construct(
        private readonly string $atmId,
        private readonly Bank $bank,
        private readonly CashDispenser $dispenserChain
    ) {
        $this->state = new IdleState();
    }

    public function insertCard(string $cardNumber): void
    {
        $this->state->insertCard($this, $cardNumber);
    }

    public function enterPin(string $pin): void
    {
        $this->state->enterPin($this, $pin);
    }

    public function checkBalance(): float
    {
        return $this->state->checkBalance($this);
    }

    /**
     * @return array<int, int>
     */
    public function withdrawCash(int $amount): array
    {
        return $this->state->withdrawCash($this, $amount);
    }

    public function ejectCard(): void
    {
        $this->state->ejectCard($this);
    }

    public function changeState(AtmState $state): void
    {
        $this->state = $state;
    }

    public function getState(): AtmState
    {
        return $this->state;
    }

    public function getBank(): Bank
    {
        return $this->bank;
    }

    public function getDispenserChain(): CashDispenser
    {
        return $this->dispenserChain;
    }

    public function setInsertedCardNumber(string $cardNumber): void
    {
        $this->insertedCardNumber = $cardNumber;
    }

    public function getInsertedCardNumber(): ?string
    {
        return $this->insertedCardNumber;
    }

    public function setCurrentAccount(Account $account): void
    {
        $this->currentAccount = $account;
    }

    public function getCurrentAccount(): ?Account
    {
        return $this->currentAccount;
    }

    public function resetSession(): void
    {
        $this->insertedCardNumber = null;
        $this->currentAccount = null;
    }

    public function getAtmId(): string
    {
        return $this->atmId;
    }
}