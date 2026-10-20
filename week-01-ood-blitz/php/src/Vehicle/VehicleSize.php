<?php 

declare(strict_types=1);

namespace App\ParkingLot\Vehicle;

enum VehicleSize: string
{
    case SMALL ='SMALL';
    case MEDIUM = 'MEDIUM';
    case LARGE = 'LARGE';
}