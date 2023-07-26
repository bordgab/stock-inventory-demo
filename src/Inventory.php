<?php

declare(strict_types=1);

namespace App;

use App\Exception\UnknownProductException;
use App\Model\Product;
use App\Model\StockItem;
use Doctrine\Common\Collections\ArrayCollection;
use Traversable;

final class Inventory implements \IteratorAggregate
{
    /**
     * @var ArrayCollection<string, StockItem>
     */
    private readonly ArrayCollection $collection;

    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public function store(Product $product, int $quantity): void
    {
        $item = new StockItem($product, $quantity);

        $articleNumber = (string)$product->getArticleNumber();

        if (!$this->collection->containsKey($articleNumber)) {
            $this->collection->set($articleNumber, $item);
        } else {
            $this->collection->get($articleNumber)->increase($quantity);
        }
    }

    /**
     * @throws UnknownProductException
     *
     * @return int  Number of successfully picked quantity (items). May be less than or equal the required quantity.
     */
    public function pick(Product $product, int $quantity): int
    {
        $pickedQuantity = \min($quantity, $this->getProductQuantity($product));
        $stock = $this->collection->get((string)$product->getArticleNumber());
        $stock->reduce($pickedQuantity);

        return $pickedQuantity;
    }

    /**
     * Returns total quantity of all stored items.
     */
    public function getVolume(): int
    {
        return $this->collection->reduce(fn($accumulator, StockItem $item) => $accumulator + $item->getQuantity(), 0);
    }

    /**
     * @throws UnknownProductException
     */
    public function getProductQuantity(Product $product): int
    {
        if (!$this->hasProduct($product)) {
            throw new UnknownProductException($product);
        }

        return $this->collection->get((string)$product->getArticleNumber())->getQuantity();
    }

    public function hasProduct(Product $product): bool
    {
        return $this->collection->containsKey((string)$product->getArticleNumber());
    }

    public function getIterator(): Traversable
    {
        return $this->collection->getIterator();
    }
}
