<?php

declare(strict_types=1);

namespace Tests\Elevator;

use App\Elevator\Car\ElevatorCar;
use App\Elevator\Controller\ElevatorController;
use App\Elevator\Model\Direction;
use App\Elevator\Model\Door;
use App\Elevator\Model\Request;
use App\Elevator\State\IdleState;
use App\Elevator\State\MovingUpState;
use App\Elevator\Strategy\LookDispatchStrategy;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ElevatorSystemTest extends TestCase
{
    public function testSafetyHazardThrowsExceptionWhenMovingWithOpenDoor(): void
    {
        $door = new Door();
        $door->open();

        $car = new ElevatorCar(id: 'CAR-1', initialFloor: 0, door: $door);
        $car->setState(new MovingUpState());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Safety Hazard: Elevator cannot move while door is open.");

        $car->step();
    }

    public function testStrategySelectsClosestIdleElevator(): void
    {
        $car1 = new ElevatorCar(id: 'CAR-1', initialFloor: 0);
        $car2 = new ElevatorCar(id: 'CAR-2', initialFloor: 8);

        $strategy = new LookDispatchStrategy();
        $request = new Request(sourceFloor: 2, direction: Direction::UP);

        $selected = $strategy->selectElevator([$car1, $car2], $request);

        $this->assertSame('CAR-1', $selected->getId());
    }

    public function testElevatorArrivesAtFloorAndTransitionsToIdle(): void
    {
        $car = new ElevatorCar(id: 'CAR-1', initialFloor: 0);
        $controller = new ElevatorController([$car]);

        // floor 1 to up (External Request)
        $controller->handleExternalRequest(sourceFloor: 1, direction: Direction::UP);

        // 1step: Elevator moves to floor 1 and opens the door
        $controller->step();
        $this->assertSame(1, $car->getCurrentFloor());
        $this->assertTrue($car->getDoor()->isOpen());

        // 2nd step: Door will close and transition to IdleState if no targets remain
        $controller->step();
        $this->assertTrue($car->getDoor()->isClosed());
        $this->assertInstanceOf(IdleState::class, $car->getState());
    }
}