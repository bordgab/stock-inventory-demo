<?php

declare(strict_types=1);

namespace App;

use App\Model\Brand;
use App\Model\Product;
use App\Model\Warehouse;

class FixturesLoader
{
    private const OPTIMAL_UTILIZATION_PERCENT = 75;

    /**
     * @var array<Warehouse>
     */
    private static array $warehouses;

    /**
     * @var array<Product>
     */
    private static array $products;

    /**
     * @var array<Brand>
     */
    private static array $brands;

    public function __construct(private readonly StockManager $stockManager)
    {
    }

    /**
     * @return array<Product>
     */
    public static function getProducts(): array
    {
        return self::$products;
    }

    /**
     * @return array<Brand>
     */
    public static function getBrands(): array
    {
        return self::$brands;
    }

    public function loadFixtures()
    {
        $fixtures = require __DIR__ . '/../config/fixtures.php';

        [
            'warehouses' => self::$warehouses,
            'products' => self::$products,
            'brands' => self::$brands
        ] = $fixtures;

        $productCount = \count(self::$products);

        foreach (self::$warehouses as $warehouse) {
            do {
                $product = self::$products[\array_rand(self::$products)];
                $quantity = \rand(1, (int)\floor($warehouse->getCapacity()/$productCount/10));
                $warehouse->storeProduct($product, $quantity);

            } while ($warehouse->getUtilizationPercent() < self::OPTIMAL_UTILIZATION_PERCENT);

            $this->stockManager->addWarehouse($warehouse);
        }
    }
}
