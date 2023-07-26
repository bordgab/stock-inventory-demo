<?php

use App\Model\ArticleNumber;
use App\Model\Brand;
use App\Model\Capacitor;
use App\Model\Resistor;
use App\Model\Semiconductor;
use App\Model\SemiconductorPackage;
use App\Model\Warehouse;

$warehouses = [
    new Warehouse('Központi raktár', 'Budapest', 10000),
    new Warehouse('Kelet magyarországi raktár', 'Miskolc', 5000),
];

$brands = [
    new Brand('ROYAL OHM', 5),
    new Brand('ST Microelectronics', 5),
    new Brand('SAMWHA', 4),
];

$products = [
    new Resistor(new ArticleNumber('R01'), 'SMD ellenállás', $brands[0], 10.0, 5, 0.25),
    new Resistor(new ArticleNumber('R02'), 'SMD ellenállás', $brands[0], 0.12E6, 5, 0.25),
    new Resistor(new ArticleNumber('R03'), 'SMD ellenállás', $brands[0], 1.2E6, 5, 0.25),
    new Semiconductor(new ArticleNumber('S01_4000'), 'Logikai kapu', $brands[1], SemiconductorPackage::DIP),
    new Semiconductor(new ArticleNumber('S01_4504'), 'Hexa tároló', $brands[1], SemiconductorPackage::DIP),
    new Capacitor(new ArticleNumber('C01'), 'Elektrolit kondenzátor', $brands[2], 12E-6, 200),
    new Capacitor(new ArticleNumber('C02'), 'Elektrolit kondenzátor', $brands[2], 47E-6, 200),
];

return [
    'warehouses' => $warehouses,
    'products' => $products,
    'brands' => $brands
];
