<?php

namespace App\Module\Feed\Basic\Module;

use App\Module\Feed\Common\Container\FeedContainerInterface;
use App\Module\Feed\Common\Module\ResolveNameModuleInterface;
use App\Module\Feed\Common\Product\ProductInterface;
use SimpleXMLElement;

final class ResolveImagesBasicFeedModule implements ResolveNameModuleInterface
{
    public const NAME = 'model';

    /**
     * {@inheritDoc}
     *
     * @param SimpleXMLElement $rawProduct
     */
    public function execute(FeedContainerInterface $container, ProductInterface $product, $rawProduct): void
    {
        $result = [];

        $rawImages = $rawProduct->xpath('Image');

        if (is_array($rawImages)) {
            foreach ($rawImages as $image) {
                $result[] = (string) $image->attributes()->{'url'};
            }

            // If not skip last image
            if (!$container->getFeed()->isRemoveLastImage()) {
                //todo ДОДЕЛАТЬ!
                //$result[] = (string) $container->getRawProducts()['Image']->attributes()->{'url'};
            }

            $result = implode(', ', $result);
        }

        //todo ДОДЕЛАТЬ!
        //$images = (string) $images->attributes()->{'url'};

        $product->setImages((string) $result);
    }
}
