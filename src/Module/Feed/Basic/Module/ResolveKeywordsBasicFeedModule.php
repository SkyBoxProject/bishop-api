<?php

namespace App\Module\Feed\Basic\Module;

use App\Module\Feed\Common\Container\FeedContainerInterface;
use App\Module\Feed\Common\Module\ResolveNameModuleInterface;
use App\Module\Feed\Common\Product\ProductInterface;
use SimpleXMLElement;

final class ResolveKeywordsBasicFeedModule implements ResolveNameModuleInterface
{
    /**
     * {@inheritDoc}
     *
     * @param SimpleXMLElement $rawProduct
     */
    public function execute(FeedContainerInterface $container, ProductInterface $product, $rawProduct): void
    {
        $product->setKeywords($product->getName());
    }
}
