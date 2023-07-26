<?php

namespace App\Exception;

use App\Model\Product;

class UnknownProductException extends RuntimeException
{
    public function __construct(Product $product)
    {
        parent::__construct(\sprintf('Unknown product: "%s"', $product));
    }
}
