<?php

declare(strict_types=1);

namespace App\Model;

class Semiconductor extends ElectronicComponent
{
    protected SemiconductorPackage $package;

    public function __construct(
        ArticleNumber $articleNumber,
        string $name,
        Brand $brand,
        SemiconductorPackage $package
    ) {
        parent::__construct($articleNumber, $name, $brand);

        $this->package = $package;
    }

    public function getPackage(): SemiconductorPackage
    {
        return $this->package;
    }
}
