<?php

declare(strict_types=1);

namespace App\Model;

abstract class AbstractProduct implements Product
{
    public function __construct(
        protected readonly ArticleNumber $articleNumber,
        protected readonly string $name,
        protected readonly Brand $brand
    ) {
    }

    public function getArticleNumber(): ArticleNumber
    {
        return $this->articleNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBrand(): Brand
    {
        return $this->brand;
    }

    public function __toString(): string
    {
        return \sprintf('[%s] %s (%s)', $this->articleNumber, $this->name, $this->brand);
    }
}
