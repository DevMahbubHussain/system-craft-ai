<?php 

declare(strict_types=1);
namespace App\VendingMachine\Product;

use InvalidArgumentException;

readonly class Product{
   
    public function __construct(
        private string $id,    
        private string $name,
        private float $price
    ){
        if ($this->price <= 0.0) {
            throw new InvalidArgumentException("Product price must be greater than zero.");
        }
    }

    public function getId(): string{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getPrice(): float{
        return $this->price;
    }

}