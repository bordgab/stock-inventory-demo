<?php

namespace App\Exception;

use App\Model\ArticleNumber;

class OutOfStockException extends RuntimeException
{
    public function __construct(ArticleNumber $articleNumber)
    {
        parent::__construct(\sprintf('Product "%s" is out of stock!', $articleNumber));
    }
}
