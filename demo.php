#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Exception\OutOfSpaceException;
use App\FixturesLoader;
use App\Model\ArticleNumber;
use App\Model\Resistor;
use App\StockManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

(new SingleCommandApplication())
    ->setCode(function (InputInterface $input, OutputInterface $output): int {
        // setting up dependency injection
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/config'));
        $loader->load('services.yaml');

        $container->compile();

        $stockManager = $container->get(StockManager::class);
        $fixtureLoader = $container->get(FixturesLoader::class);

        // load fixtures
        $output->writeln(\sprintf('<info>Filling up warehouses from fixtures.</info>'));
        $fixtureLoader->loadFixtures();
        $stockManager->dumpStock($output);

        // storing new product
        $brands = $fixtureLoader->getBrands();
        $product = new Resistor(new ArticleNumber('R04'), 'Fémréteg ellenállás', $brands[0], 1E3, 10, 0.5);
        $qty = 5000;
        try {
            $output->writeln(\sprintf('<info>Store new product: </info>%s, <info>quantity:</info>  %d', $product, $qty));
            $stockManager->store($product, $qty);

            $stockManager->dumpStock($output);
        } catch (OutOfSpaceException $e) {
            $output->writeln(\sprintf('<error>%s</error>', $e->getMessage()));
            $qty -= $e->getMissedQuantity();
        }
        $stockManager->dumpStock($output);

        // picking previously stored product
        $output->writeln(\sprintf('<info>Pick product: </info>%s, <info>quantity:</info>  %d', $product, $qty-1));
        $stockManager->pick('R04', $qty-1);

        $stockManager->dumpStock($output);

        return 0;
    })
    ->run()
;

