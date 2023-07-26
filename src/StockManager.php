<?php

declare(strict_types=1);

namespace App;

use App\Exception\OutOfStockException;
use App\Exception\OutOfSpaceException;
use App\Exception\RuntimeException;
use App\Model\Product;
use App\Model\StockItem;
use App\Model\Warehouse;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;

class StockManager
{
    /**
     * @var ArrayCollection<Warehouse>
     */
    private ArrayCollection $warehouses;

    public function __construct(array $warehouses = [])
    {
        $this->warehouses = new ArrayCollection($warehouses);
    }

    public function addWarehouse(Warehouse $warehouse): void
    {
        $this->warehouses->add($warehouse);
    }

    /**
     * @throws OutOfSpaceException  If all warehouses are full, no more products can be stocked.
     */
    public function store(Product $product, int $quantity): void
    {
        if ($this->warehouses->isEmpty()) {
            throw new RuntimeException('No available warehouses present.');
        }

        $nextWarehouse = $this->warehouses->first();

        do {
            $actualWarehouse = $nextWarehouse;
            $quantity -= $actualWarehouse->storeProduct($product, $quantity);
            $nextWarehouse = $this->warehouses->next();
        } while (0 < $quantity && $nextWarehouse);

        if (0 < $quantity) {
            throw new OutOfSpaceException($actualWarehouse, $quantity);
        }
    }

    /**
     * @throws OutOfStockException  If the required product quantity cannot be fulfilled.
     */
    public function pick(Product $product, int $quantity): void
    {
        if ($this->warehouses->isEmpty()) {
            throw new RuntimeException('No available warehouses present.');
        }

        $warehouse = $this->warehouses->first();

        do {
            $quantity -= $warehouse->pickProduct($product, $quantity);
            $warehouse = $this->warehouses->next();
        } while (0 < $quantity && $warehouse);

        if (0 < $quantity) {
            throw new OutOfStockException($product);
        }
    }

    public function dumpStock(OutputInterface $output): void
    {

        foreach ($this->warehouses as $warehouse) {
            $output->writeln(PHP_EOL.\sprintf('  <info>%s:</info>', $warehouse));

            $table = new Table($output);
            $table
                ->setStyle('box')
                ->setHeaders(['SKU', 'Product name', 'Brand', 'Quantity'])
                ->setRows(\array_map(static function (StockItem $item) {
                    $product = $item->getProduct();
                    return [
                        $product->getArticleNumber(),
                        $product->getName(),
                        $product->getBrand(),
                        $item->getQuantity()
                    ];
                }, \iterator_to_array($warehouse->getStockItems())))
            ;

            $table->render();
        }
        $output->writeln('');
    }
}
