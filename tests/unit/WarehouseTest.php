<?php

namespace App\Tests\unit;

use App\Model\ArticleNumber;
use App\Model\Brand;
use App\Model\Product;
use App\Model\Semiconductor;
use App\Model\SemiconductorPackage;
use App\Model\Warehouse;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class WarehouseTest extends TestCase
{
    /**
     * @var ArrayCollection<Product>
     */
    private readonly ArrayCollection $products;

    public function setUp(): void
    {
        $this->products = new ArrayCollection();

        $brand = new Brand('ST Microelectronics', 5);

        $this->products->add(new Semiconductor(new ArticleNumber('CD4001'), 'CMOS NOR Gate', $brand, SemiconductorPackage::DIL));
        $this->products->add(new Semiconductor(new ArticleNumber('CD4012'), 'CMOS NAND Gate', $brand, SemiconductorPackage::DIL));
    }

    /**
     * @covers \App\Model\Warehouse::storeProduct
     * @covers \App\Model\Warehouse::getProductQuantity
     * @covers \App\Model\Warehouse::getUtilizationPercent
     */
    public function testStoreProducts(): Warehouse
    {
        $warehouse = new Warehouse('Központi raktár', 'Budapest', 1000);

        $warehouse->storeProduct($this->products[0], 500);
        $warehouse->storeProduct($this->products[1], 100);
        $this->assertSame(500, $warehouse->getProductQuantity($this->products[0]->getArticleNumber()));
        $this->assertSame(100, $warehouse->getProductQuantity($this->products[1]->getArticleNumber()));
        $this->assertSame(60, $warehouse->getUtilizationPercent());

        return $warehouse;
    }

    /**
     * @covers \App\Model\Warehouse::storeProduct
     * @depends testStoreProducts
     */
    public function testExceedingCapacity(Warehouse $warehouse): Warehouse
    {
        $this->assertSame(400, $warehouse->storeProduct($this->products[1], 520));

        return $warehouse;
    }
}
