<?php

namespace App\Tests\unit;

use App\Exception\OutOfSpaceException;
use App\Model\ArticleNumber;
use App\Model\Brand;
use App\Model\Product;
use App\Model\Semiconductor;
use App\Model\SemiconductorPackage;
use App\Model\Warehouse;
use App\StockManager;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class StockManagerTest extends TestCase
{
    /**
     * @var ArrayCollection<Product>
     */
    private readonly ArrayCollection $products;

    /**
     * @var ArrayCollection<Warehouse>
     */
    private readonly ArrayCollection $warehouses;

    public function setUp(): void
    {
        $brand = new Brand('ST Microelectronics', 5);

        $this->products = new ArrayCollection();
        $this->products->add(new Semiconductor(new ArticleNumber('CD4001'), 'CMOS NOR Gate', $brand, SemiconductorPackage::DIL));
        $this->products->add(new Semiconductor(new ArticleNumber('CD4012'), 'CMOS NAND Gate', $brand, SemiconductorPackage::DIL));

        $this->warehouses = new ArrayCollection();
        $this->warehouses->add(new Warehouse('Északi', 'Nyíregyháza', 500));
        $this->warehouses->add(new Warehouse('Déli', 'Pécs', 1000));
    }

    /**
     * @covers \App\StockManager::addWarehouse
     * @covers \App\StockManager::store
     */
    public function testOutOfSpace(): StockManager
    {
        $stockManager = new StockManager();
        $stockManager->addWarehouse($this->warehouses[0]);
        $stockManager->addWarehouse($this->warehouses[1]);

        $this->expectException(OutOfSpaceException::class);
        $this->expectExceptionMessageMatches('/Pécs/');

        $stockManager->store($this->products[0], 250);
        $stockManager->store($this->products[1], 750);
        $stockManager->store($this->products[0], 400);
        $stockManager->store($this->products[1], 500);

        return $stockManager;
    }

    /**
     * @covers \App\StockManager::addWarehouse
     * @covers \App\StockManager::store
     * @covers \App\StockManager::pick
     * @covers \App\Model\Warehouse::getProductQuantity
     * @covers \App\Model\Warehouse::isFull
     */
    public function testPickFromMultipleWarehouse(): void
    {
        $stockManager = new StockManager();
        $stockManager->addWarehouse($this->warehouses[0]);
        $stockManager->addWarehouse($this->warehouses[1]);

        $stockManager->store($this->products[0], 250);
        $stockManager->store($this->products[1], 750);
        $stockManager->store($this->products[0], 400);
        $stockManager->store($this->products[1], 100);

        $this->assertSame(250, $this->warehouses[0]->getProductQuantity($this->products[0]->getArticleNumber()));
        $this->assertSame(250, $this->warehouses[0]->getProductQuantity($this->products[1]->getArticleNumber()));
        $this->assertSame(400, $this->warehouses[1]->getProductQuantity($this->products[0]->getArticleNumber()));
        $this->assertSame(600, $this->warehouses[1]->getProductQuantity($this->products[1]->getArticleNumber()));

        $this->assertTrue($this->warehouses[0]->isFull());
        $this->assertTrue($this->warehouses[1]->isFull());

        $stockManager->pick($this->products[0]->getArticleNumber(), 600);

        $this->assertSame(0, $this->warehouses[0]->getProductQuantity($this->products[0]->getArticleNumber()));
        $this->assertSame(50, $this->warehouses[1]->getProductQuantity($this->products[0]->getArticleNumber()));
    }
}
