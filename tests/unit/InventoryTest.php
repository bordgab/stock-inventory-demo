<?php

namespace App\Tests\unit;

use App\Exception\UnknownProductException;
use App\Inventory;
use App\Model\ArticleNumber;
use App\Model\Brand;
use App\Model\Product;
use App\Model\Semiconductor;
use App\Model\SemiconductorPackage;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class InventoryTest extends TestCase
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
     * @covers \App\Inventory::store
     * @covers \App\Inventory::getVolume
     */
    public function testStore(): Inventory
    {
        $inventory = new Inventory();

        $inventory->store($this->products[0], 200);
        $this->assertSame(200, $inventory->getVolume());

        $inventory->store($this->products[1], 100);
        $this->assertSame(300, $inventory->getVolume());

        return $inventory;
    }

    /**
     * @covers \App\Inventory::pick
     * @covers \App\Inventory::getVolume
     * @depends testStore
     */
    public function testPick(Inventory $inventory): Inventory
    {
        $inventory->pick($this->products[0]->getArticleNumber(), 199);
        $inventory->pick($this->products[1]->getArticleNumber(), 99);

        $this->assertSame(2, $inventory->getVolume());

        $this->assertSame(1, $inventory->pick($this->products[0]->getArticleNumber(), 100));
        $this->assertSame(1, $inventory->pick($this->products[1]->getArticleNumber(), 100));

        $this->assertSame(0, $inventory->pick($this->products[0]->getArticleNumber(), 1));
        $this->assertSame(0, $inventory->pick($this->products[1]->getArticleNumber(), 1));

        return $inventory;
    }

    /**
     * @covers \App\Inventory::pick
     * @covers \App\Inventory::store
     * @covers \App\Inventory::getVolume
     * @depends testPick
     */
    public function testPickUnavailableQuantity(Inventory $inventory): void
    {
        $this->assertSame(0, $inventory->getVolume());

        $inventory->store($this->products[0], 4);
        $this->assertSame(4, $inventory->getVolume());

        $inventory->store($this->products[1], 5);
        $this->assertSame(9, $inventory->getVolume());


        $this->assertSame(4, $inventory->pick($this->products[0]->getArticleNumber(), 100));
        $this->assertSame(5, $inventory->pick($this->products[1]->getArticleNumber(), 100));

        $this->assertSame(0, $inventory->getVolume());
    }

    /**
     * @covers \App\Inventory::pick
     * @covers \App\Inventory::store
     * @covers \App\Inventory::getProductQuantity
     */
    public function testProductQuantity(): void
    {
        $inventory = new Inventory();

        $inventory->store($this->products[0], 10);
        $inventory->store($this->products[1], 8);

        $this->assertSame(10, $inventory->getProductQuantity($this->products[0]->getArticleNumber()));
        $this->assertSame(8, $inventory->getProductQuantity($this->products[1]->getArticleNumber()));

        $inventory->pick($this->products[0]->getArticleNumber(), 1);
        $inventory->pick($this->products[1]->getArticleNumber(), 1);

        $this->assertSame(9, $inventory->getProductQuantity($this->products[0]->getArticleNumber()));
        $this->assertSame(7, $inventory->getProductQuantity($this->products[1]->getArticleNumber()));
    }

    public function testPickUnknownProduct(): void
    {
        $inventory = new Inventory();

        $this->expectException(UnknownProductException::class);

        $inventory->pick('szalámi', 10);
    }
}
