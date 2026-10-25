<?php

declare(strict_types=1);

namespace App\MovieBooking;

readonly class Movie
{


    public function __construct(
        private string $id,
        private string $title,
        private int $durationInMinutes,
        private string $genre
    ) {

        if ($this->durationInMinutes <= 0) {
            throw new \InvalidArgumentException("Duration must be greater than 0");
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDurationInMinutes(): int
    {
        return $this->durationInMinutes;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }
}
