<?php

namespace App\Module\Feed\Basic\Module;

use App\Module\Feed\Common\Container\FeedContainerInterface;
use App\Module\Feed\Common\Module\ResolveNameModuleInterface;
use App\Module\Feed\Common\Product\ProductInterface;
use SimpleXMLElement;
use Throwable;

final class ResolveDescriptionBasicFeedModule implements ResolveNameModuleInterface
{
    public const DESCRIPTION = 'description';

    /**
     * {@inheritDoc}
     *
     * @param SimpleXMLElement $rawProduct
     */
    public function execute(FeedContainerInterface $container, ProductInterface $product, $rawProduct): void
    {
        try {
            $product->setDescription((string) $rawProduct->{self::DESCRIPTION});
        } catch (Throwable $exception) {
        }
    }
}
