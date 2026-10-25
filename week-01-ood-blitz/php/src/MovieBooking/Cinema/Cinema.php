<?php

declare(strict_types=1);

namespace App\MovieBooking\Cinema;

use InvalidArgumentException;

class Cinema
{
    private array $rooms = [];

    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $city
    ) {}


    public function addRoom(Room $room): void
    {
        if (isset($this->rooms[$room->getId()])) {
            throw new InvalidArgumentException("Room with ID {$room->getId()} already exists in this cinema.");
        }
        $this->rooms[$room->getId()] = $room;
    }

    public function getRoom(string $roomId): ?Room
    {
        return $this->rooms[$roomId] ?? null;
    }

    public function getRooms(): array
    {
        return $this->rooms;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCity(): string
    {
        return $this->city;
    }
}
