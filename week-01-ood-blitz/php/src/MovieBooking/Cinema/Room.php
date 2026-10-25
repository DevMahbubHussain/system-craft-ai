<?php 

declare(strict_types=1);

namespace App\MovieBooking\Cinema;

class Room{
    public function __construct(
        private string $id,
        private string $name,
        private Layout $layout
    ){}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLayout(): Layout
    {
        return $this->layout;
    }

    public function getCapacity(): int
    {
        return $this->layout->getTotalCapacity();
    }   

}