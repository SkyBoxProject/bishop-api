<?php

namespace App\Module\Feed\Basic\Module;

use App\Module\Feed\Common\Container\FeedContainerInterface;
use App\Module\Feed\Common\Module\ResolveNameModuleInterface;
use App\Module\Feed\Common\Product\ProductInterface;
use SimpleXMLElement;

final class ResolveNameBasicFeedModule implements ResolveNameModuleInterface
{
    public const NAME = 'model';

    /**
     * {@inheritDoc}
     *
     * @param SimpleXMLElement $rawParameters
     */
    public function execute(FeedContainerInterface $container, ProductInterface $product, $rawProduct): void
    {
        $name = (string) $rawProduct->{self::NAME};

        $product->setName($name);
    }
}
