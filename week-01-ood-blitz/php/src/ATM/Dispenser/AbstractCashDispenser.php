<?php

declare(strict_types=1);

namespace App\ATM\Dispenser;

use InvalidArgumentException;
use RuntimeException;

abstract class AbstractCashDispenser implements CashDispenser
{
    private ?CashDispenser $next = null;

    public function __construct(
        protected readonly int $denomination,
        protected int $availableNotes
    ) {
        if ($this->denomination <= 0) {
            throw new InvalidArgumentException("Denomination must be positive.");
        }
    }

    public function setNext(CashDispenser $nextDispenser): CashDispenser
    {
        $this->next = $nextDispenser;
        return $nextDispenser;
    }

    public function dispense(int $amount, array &$dispensedNotes): array
    {
        $notesToDispense = 0;
        if ($amount >= $this->denomination && $this->availableNotes > 0) {
            $requiredNotes = intdiv($amount, $this->denomination);
            $notesToDispense = min($requiredNotes, $this->availableNotes);
        }

        $remaining = $amount - ($notesToDispense * $this->denomination);

        if ($remaining > 0) {
            if ($this->next === null) {
                throw new RuntimeException("ATM cannot dispense the exact requested amount with current cash inventory.");
            }
            $this->next->dispense($remaining, $dispensedNotes);
        }

        // Commit only after the downstream chain succeeded, so a failure
        // anywhere leaves the vault inventory untouched. Prepending keeps
        // the result ordered high-to-low denomination (100 first).
        $this->availableNotes -= $notesToDispense;
        if ($notesToDispense > 0) {
            $dispensedNotes = [$this->denomination => $notesToDispense] + $dispensedNotes;
        }

        return $dispensedNotes;
    }

    public function getAvailableNotes(): int
    {
        return $this->availableNotes;
    }
}
