<?php

declare(strict_types=1);

namespace App\MovieBooking\Seat;

use DateTimeImmutable;
use RuntimeException;

class ShowSeat
{
    private SeatStatus $status = SeatStatus::AVAILABLE;
    private ?DateTimeImmutable $heldUntil = null;

    public function __construct(
        private readonly string $seatNumber,
        private readonly SeatType $seatType,
        private readonly float $basePrice
    ) {}


    public function isAvailable(): bool
    {
        if ($this->status === SeatStatus::AVAILABLE) {
            return true;
        }

        // যদি HELD থাকে কিন্তু হোল্ডের টাইম পার হয়ে যায়, তবে এটি আবার Available
        if ($this->status === SeatStatus::HELD && $this->heldUntil !== null) {
            return new DateTimeImmutable() > $this->heldUntil;
        }

        return false;
    }

    public function hold(int $holdDurationInSeconds): void
    {
        if (!$this->isAvailable()) {
            throw new RuntimeException("Seat {$this->seatNumber} cannot be held (already booked or held by another user).");
        }
        $this->status = SeatStatus::HELD;
        $this->heldUntil = new DateTimeImmutable("+{$holdDurationInSeconds} seconds");
    }

    public function canBook(): bool
    {
        return $this->status === SeatStatus::HELD
            && ($this->heldUntil === null || new DateTimeImmutable() <= $this->heldUntil);
    }

    public function book(): void
    {
        // সিটটি অবশ্যই HELD অবস্থায় থাকতে হবে এবং টাইমআউট এখনো ভ্যালিড থাকতে হবে
        if (!$this->canBook()) {
            throw new RuntimeException("Seat {$this->seatNumber} must be actively held before confirming booking.");
        }

        $this->status = SeatStatus::BOOKED;
        $this->heldUntil = null;
    }
    public function release(): void
    {
        if ($this->status === SeatStatus::BOOKED) {
            throw new RuntimeException("Cannot release an already confirmed booked seat without refund processing.");
        }

        $this->status = SeatStatus::AVAILABLE;
        $this->heldUntil = null;
    }

    public function getFinalPrice(): float
    {
        return $this->basePrice * $this->seatType->priceMultiplier();
    }

    public function getSeatNumber(): string
    {
        return $this->seatNumber;
    }

    public function getSeatType(): SeatType
    {
        return $this->seatType;
    }

    public function getStatus(): SeatStatus
    {
        return $this->status;
    }

    public function getHeldUntil(): ?DateTimeImmutable
    {
        return $this->heldUntil;
    }


}
