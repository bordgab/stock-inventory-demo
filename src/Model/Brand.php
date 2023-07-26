<?php

declare(strict_types=1);

namespace App\Model;

class Brand
{
    public function __construct(private readonly string $name, private readonly int $quality)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
