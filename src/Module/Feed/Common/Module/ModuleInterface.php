<?php

namespace App\Module\Feed\Common\Module;

use App\Module\Feed\Common\Container\FeedContainerInterface;
use App\Module\Feed\Common\Exception\SkipRawProductException;
use App\Module\Feed\Common\Product\ProductInterface;

interface ModuleInterface
{
    /**
     * @param mixed $rawProduct
     *
     * @throws SkipRawProductException
     */
    public function execute(FeedContainerInterface $container, ProductInterface $product, $rawProduct): void;
}
