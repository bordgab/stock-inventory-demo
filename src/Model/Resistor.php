<?php

declare(strict_types=1);

namespace App\Model;

use App\Math\Util;

class Resistor extends PassiveComponent
{
    protected float $value;
    protected int $tolerance;
    protected float $power;

    public function __construct(
        ArticleNumber $articleNumber,
        string $name,
        Brand $brand,
        float $value,
        int $tolerance,
        float $power
    ) {
        parent::__construct($articleNumber, $name, $brand);

        $this->value = $value;
        $this->tolerance = $tolerance;
        $this->power = $power;
    }

    public function __toString(): string
    {
        return \sprintf('[%s] %s (%s) - %.3f Ohm / %.3f%% / %.3f Watt ',
            $this->articleNumber,
            $this->name,
            $this->brand,
            Util::siPrefixedFormat($this->value),
            $this->tolerance,
            $this->power
        );
    }
}
