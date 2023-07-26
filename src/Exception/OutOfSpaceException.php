<?php

namespace App\Exception;

use App\Model\Warehouse;

class OutOfSpaceException extends RuntimeException
{
    public function __construct(private readonly Warehouse $warehouse, private readonly int $missedQuantity)
    {
        parent::__construct(\sprintf('Warehouse "%s" is out of space, %d product(s) cannot be stored.', $this->warehouse, $this->missedQuantity));
    }

    public function getWarehouse(): Warehouse
    {
        return $this->warehouse;
    }

    public function getMissedQuantity(): int
    {
        return $this->missedQuantity;
    }
}
