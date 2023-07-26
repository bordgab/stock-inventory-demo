<?php

declare(strict_types=1);

namespace App\Model;

use App\Math\Util;

class Capacitor extends PassiveComponent
{
    protected float $value;
    protected int $voltage;

    public function __construct(
        ArticleNumber $articleNumber,
        string $name,
        Brand $brand,
        float $value,
        int $voltage,
    ) {
        parent::__construct($articleNumber, $name, $brand);

        $this->value = $value;
        $this->voltage = $voltage;
    }

    public function __toString(): string
    {
        return \sprintf('[%s] %s (%s) - %f Farad / %d Volt ',
            $this->articleNumber,
            $this->name,
            $this->brand,
            Util::siPrefixedFormat($this->value),
            $this->voltage
        );
    }


}