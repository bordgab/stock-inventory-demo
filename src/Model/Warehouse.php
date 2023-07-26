<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\UnknownProductException;
use App\Inventory;

class Warehouse
{
    private readonly Inventory $inventory;

    public function __construct(
        private readonly string $name,
        private readonly string $address,
        private readonly int $capacity,
    ) {
        $this->inventory = new Inventory();
    }

    /**
     * @return int  Number of successfully stored product quantity (items). May be less than or equal than the required quantity (depending on warehouse capacity).
     */
    public function storeProduct(Product $product, int $quantity): int
    {
        $quantity = min($this->capacity - $this->inventory->getVolume(), $quantity);

        $this->inventory->store($product, $quantity);

        return $quantity;
    }

    /**
     * @throws UnknownProductException
     *
     * @return int  Number of successfully picked quantity (items). May be less than or equal than the required quantity.
     */
    public function pickProduct(ArticleNumber|string $articleNumber, int $quantity): int
    {
        return $this->inventory->pick($articleNumber, $quantity);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getVolume(): int
    {
        return $this->inventory->getVolume();
    }

    public function getUtilizationPercent(): int
    {
        return (int)\floor($this->inventory->getVolume() / $this->capacity * 100);
    }

    public function isFull(): bool
    {
        return $this->capacity === $this->inventory->getVolume();
    }

    /**
     * @return iterable<StockItem>
     * @throws \Exception
     */
    public function getStockItems(): iterable
    {
        return $this->inventory->getIterator();
    }

    /**
     * @throws UnknownProductException
     */
    public function getProductQuantity(ArticleNumber|string $articleNumber): int
    {
        return $this->inventory->getProductQuantity($articleNumber);
    }

    public function __toString(): string
    {
        return \sprintf('%s (%s, capacity: %d, space left: %d, utilization: %d%%)', $this->name, $this->address, $this->capacity, $this->capacity-$this->inventory->getVolume(), $this->getUtilizationPercent());
    }
}
