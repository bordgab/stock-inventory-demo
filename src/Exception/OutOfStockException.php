<?php

namespace App\Exception;

use App\Model\Product;

class OutOfStockException extends RuntimeException
{
    public function __construct(Product $product)
    {
        parent::__construct(\sprintf('Product "%s" is out of stock!', $product));
    }
}
