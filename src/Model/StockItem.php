<?php

declare(strict_types=1);

namespace App\Model;

class StockItem
{
    public function __construct(private readonly Product $product, private int $quantity)
    {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function reduce(int $amount = 1): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException(\sprintf('Amount must be greater than 0, given: %d', $amount));
        }

        if (0 > $this->quantity - $amount) {
            throw new \OutOfBoundsException('Product quantity cannot be reduced below 0 (product shortage not allowed!).');
        }

        $this->quantity -= $amount;
    }

    public function increase(int $amount = 1): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException(\sprintf('Amount must be greater than 0, given: %d', $amount));
        }

        $this->quantity += $amount;
    }
}
