<?php

namespace App\Module\Feed\Basic\Module;

use App\Module\Feed\Common\Container\FeedContainerInterface;
use App\Module\Feed\Common\Module\ResolveNameModuleInterface;
use App\Module\Feed\Common\Product\ProductInterface;
use SimpleXMLElement;

final class ResolveAddedCityBasicFeedModule implements ResolveNameModuleInterface
{
    public const NAME = 'model';

    /**
     * {@inheritDoc}
     *
     * @param SimpleXMLElement $rawProduct
     */
    public function execute(FeedContainerInterface $container, ProductInterface $product, $rawProduct): void
    {
        if (empty($container->getFeed()->getAddedCity())) {
            return;
        }

        $product->setKeywords(sprintf('%s купить в %s', $product->getKeywords(), $container->getFeed()->getAddedCity()));
    }
}
