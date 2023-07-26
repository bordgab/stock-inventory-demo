<?php

declare(strict_types=1);

namespace App\Model;

final class ArticleNumber
{
    public function __construct(private readonly string $id)
    {
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
