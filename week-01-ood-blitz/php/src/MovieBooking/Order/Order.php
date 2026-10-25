<?php

declare(strict_types=1);

namespace App\MovieBooking\Order;

use App\MovieBooking\Screening\Screening;
use App\MovieBooking\Seat\ShowSeat;
use InvalidArgumentException;
use RuntimeException;

class Order
{
    private OrderStatus $status = OrderStatus::PENDING;
    private array $issuedTickets = [];

    public function __construct(
        private readonly string $orderId,
        private readonly string $customerEmail,
        private readonly Screening $screening,
        private readonly array $seats
    ) {
        if (empty($this->seats)) {
            throw new InvalidArgumentException("An order must contain at least one seat.");
        }
    }

    public function calculateTotal(): float
    {
        return array_reduce(
            $this->seats,
            fn(float $carry, ShowSeat $seat) => $carry + $seat->getFinalPrice(),
            0.0
        );
    }

    public function confirmPayment(): array
    {
        if ($this->status !== OrderStatus::PENDING) {
            throw new RuntimeException("Only pending orders can be confirmed.");
        }

        // Validate every seat first so a failure never leaves the order half-booked.
        foreach ($this->seats as $seat) {
            if (!$seat->canBook()) {
                throw new RuntimeException(
                    "Order {$this->orderId} cannot be confirmed: seat {$seat->getSeatNumber()} is not actively held."
                );
            }
        }

        foreach ($this->seats as $seat) {
            $seat->book();

            $ticketId = sprintf('TCK-%s-%s', $this->orderId, $seat->getSeatNumber());
            $this->issuedTickets[] = new Ticket(
                $ticketId,
                $seat->getSeatNumber(),
                $this->screening->getMovie()->getTitle(),
                $this->screening->getRoom()->getName(),
                $this->screening->getStartTime(),
                $seat->getFinalPrice(),
            );
        }

        $this->status = OrderStatus::CONFIRMED;

        return $this->issuedTickets;
    }


    public function cancel(): void
    {
        if ($this->status !== OrderStatus::PENDING) {
            throw new RuntimeException("Only pending orders can be cancelled.");
        }

        foreach ($this->seats as $seat) {
            $seat->release();
        }

        $this->status = OrderStatus::CANCELLED;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getSeats(): array
    {
        return $this->seats;
    }


    public function getIssuedTickets(): array
    {
        return $this->issuedTickets;
    }
}
