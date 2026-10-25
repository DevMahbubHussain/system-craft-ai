<?php 
declare(strict_types=1);

namespace App\MovieBooking\Screening;

use App\MovieBooking\Cinema\Room;
use App\MovieBooking\Movie;
use App\MovieBooking\Seat\SeatType;
use App\MovieBooking\Seat\ShowSeat;
use DateTimeImmutable;
use RuntimeException;

class Screening{
   
   private array $showSeats = [];

   public function __construct(
        private readonly string $id,
        private readonly Movie $movie,
        private readonly Room $room,
        private readonly \DateTimeImmutable $startTime,
        private readonly float $baseSeatPrice = 10.0
   
    ){
        $this->generateSeats();
    }

    private function generateSeats(): void
    {
        $layout = $this->room->getLayout();
        $totalRows = $layout->getTotalRows();
        $seatsPerRow = $layout->getSeatsPerRow();

        for ($r = 1; $r <= $totalRows; $r++) {
            $rowLetter = chr(64 + $r); 

            $seatType = match (true) {
                $r === $totalRows => SeatType::VIP,
                $r >= $totalRows - 1 => SeatType::PREMIUM,
                default => SeatType::REGULAR,
            };

            for ($s = 1; $s <= $seatsPerRow; $s++) {
                $seatNumber = sprintf('%s%d', $rowLetter, $s); 
                $this->showSeats[$seatNumber] = new ShowSeat(
                    $seatNumber,
                    $seatType,
                    $this->baseSeatPrice
                );
            }
        }
    }

    public function getSeat(string $seatNumber): ShowSeat
    {
        if (!isset($this->showSeats[$seatNumber])) {
            throw new RuntimeException("Seat {$seatNumber} does not exist in this screening.");
        }

        return $this->showSeats[$seatNumber];
    }


    public function getAvailableSeats(): array
    {
        return array_filter(
            $this->showSeats,
            fn(ShowSeat $seat) => $seat->isAvailable()
        );
    }
    public function getId(): string
    {
        return $this->id;
    }

    public function getMovie(): Movie
    {
        return $this->movie;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getAllSeats(): array
    {
        return $this->showSeats;
    }

}