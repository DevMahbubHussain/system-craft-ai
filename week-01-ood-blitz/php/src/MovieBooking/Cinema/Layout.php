<?php 

declare(strict_types=1);

namespace App\MovieBooking\Cinema;

readonly class Layout{
    
    public function __construct(
        private int $totalRows,
        private int $seatsPerRow
    ) {
        if ($this->totalRows <= 0) {
            throw new \InvalidArgumentException("Total rows must be greater than 0");
        }
        if ($this->seatsPerRow <= 0) {
            throw new \InvalidArgumentException("Seats per row must be greater than 0");
        }
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getSeatsPerRow(): int
    {
        return $this->seatsPerRow;
    }

    public function getTotalCapacity(): int
    {
        return $this->totalRows * $this->seatsPerRow;
    }
  
}