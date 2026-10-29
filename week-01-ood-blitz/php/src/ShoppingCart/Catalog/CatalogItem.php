<?php

declare(strict_types=1);

namespace App\ShoppingCart\Catalog;

interface CatalogItem
{
    public function getId(): string;

    public function getName(): string;

    public function getPrice(): float;

    public function isAvailable(): bool;

  

}
