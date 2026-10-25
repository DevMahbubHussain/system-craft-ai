<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\MovieBooking\Cinema\Cinema;
use App\MovieBooking\Cinema\Layout;
use App\MovieBooking\Cinema\Room;
use App\MovieBooking\Movie;
use App\MovieBooking\Order\Order;
use App\MovieBooking\Screening\Screening;

echo "====================================================\n";
echo "    MOVIE TICKET BOOKING SYSTEM - PURE OOP DEMO     \n";
echo "====================================================\n\n";

// Movie Setup
$movie = new Movie('MOV-001', 'Inception', 148, 'Sci-Fi / Thriller');
echo "[Movie Created] {$movie->getTitle()} ({$movie->getDurationInMinutes()} mins) | Genre: {$movie->getGenre()}\n";

// Cinema & Room Setup
$layout = new Layout(3, 4);
$room = new Room('RM-101', 'Hall-1 (IMAX)', $layout);

$cinema = new Cinema('CIN-DHK-01', 'Star Cineplex', 'Dhaka');
$cinema->addRoom($room);
echo "[Cinema Setup] {$cinema->getName()} ({$cinema->getCity()}) -> Room: {$room->getName()} (Capacity: {$room->getCapacity()})\n\n";

//  (Screening Bridge)
$showTime = new DateTimeImmutable('+2 hours');
$screening = new Screening('SCR-9901', $movie, $room, $showTime, 12.0); // Base price $12
echo "[Screening Scheduled] Movie: {$screening->getMovie()->getTitle()} at {$screening->getStartTime()->format('Y-m-d H:i')}\n";
echo "[Seat Inventory] Available Seats: " . count($screening->getAvailableSeats()) . " / {$room->getCapacity()}\n\n";

// (Concurrency & Lock Simulation)
echo "--- Step 1: User selects seats A1 and C4 ---\n";
$seatA1 = $screening->getSeat('A1');
$seatC4 = $screening->getSeat('C4');

// (Hold for 300 seconds)
$seatA1->hold(300);
$seatC4->hold(300);
echo "Seat {$seatA1->getSeatNumber()} ({$seatA1->getSeatType()->value}) Status: {$seatA1->getStatus()->value}\n";
echo "Seat {$seatC4->getSeatNumber()} ({$seatC4->getSeatType()->value}) Status: {$seatC4->getStatus()->value}\n";
echo "Remaining Available Seats in Hall: " . count($screening->getAvailableSeats()) . "\n\n";

// Try to hold an already held seat to test guard clause
try {
    echo "[Conflict Test] Another user attempts to hold A1...\n";
    $seatA1->hold(300);
} catch (RuntimeException $e) {
    echo "Guard Clause Passed: " . $e->getMessage() . "\n\n";
}

// Order simulation
echo "--- Step 2: Creating Order ---\n";
$order = new Order(
    'ORD-2026-88',
    'customer@example.com',
    $screening,
    [$seatA1, $seatC4]
);

echo "Order #{$order->getOrderId()} created for {$order->getCustomerEmail()}\n";
echo "Order Status: {$order->getStatus()->value}\n";
echo "Total Billed Amount: \${$order->calculateTotal()}\n\n";

//Payment simulation (Assuming payment is successful)
echo "--- Step 3: Payment Processed & Confirming Booking ---\n";
$tickets = $order->confirmPayment();

echo "Order Status: {$order->getStatus()->value}\n";
echo "Digital Tickets Generated (" . count($tickets) . "):\n";

foreach ($tickets as $ticket) {
    echo "----------------------------------------\n";
    echo " Ticket ID   : {$ticket->getTicketId()}\n";
    echo " Movie       : {$ticket->getMovieTitle()}\n";
    echo " Auditorium  : {$ticket->getRoomName()}\n";
    echo " Seat Number : {$ticket->getSeatNumber()}\n";
    echo " Show Time   : {$ticket->getShowTime()->format('Y-m-d H:i')}\n";
    echo " Price Paid  : \${$ticket->getPricePaid()}\n";
}
echo "----------------------------------------\n\n";

// Final Verification
echo "[Final Verification]\n";
echo "Seat A1 Status: {$seatA1->getStatus()->value}\n";
echo "Seat C4 Status: {$seatC4->getStatus()->value}\n";
echo "Available Seats Remaining for this Screening: " . count($screening->getAvailableSeats()) . "\n\n";

echo "====================================================\n";
echo "  MOVIE BOOKING DOMAIN VERIFIED SUCCESSFULLY!       \n";
echo "====================================================\n";