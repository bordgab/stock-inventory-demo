<?php

namespace App\Exception;

use App\Model\ArticleNumber;

class UnknownProductException extends RuntimeException
{
    public function __construct(ArticleNumber|string $articleNumber)
    {
        parent::__construct(\sprintf('Unknown product: "%s"', $articleNumber));
    }
}
