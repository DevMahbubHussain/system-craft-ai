<?php

declare(strict_types=1);

namespace App\MovieBooking\Order;

use DateTimeImmutable;

class Ticket
{
    public function __construct(
        private string $ticketId,
        private string $seatNumber,
        private string $movieTitle,
        private string $roomName,
        private DateTimeImmutable $showTime,
        private float $pricePaid
    ) {}
    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    public function getSeatNumber(): string
    {
        return $this->seatNumber;
    }

    public function getMovieTitle(): string
    {
        return $this->movieTitle;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }

    public function getShowTime(): DateTimeImmutable
    {
        return $this->showTime;
    }

    public function getPricePaid(): float
    {
        return $this->pricePaid;
    }
}
