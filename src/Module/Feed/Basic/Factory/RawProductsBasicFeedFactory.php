<?php

namespace App\Module\Feed\Basic\Factory;

use App\Domain\Feed\Entity\Feed;
use App\Module\Feed\Common\Container\FeedContainerInterface;
use App\Module\Feed\Common\Factory\ContainerFactoryInterface;
use SimpleXMLElement;

final class RawProductsBasicFeedFactory implements ContainerFactoryInterface
{
    public const SOURCE_PRODUCTS = 'products';
    public const SOURCE_PRODUCT = 'product';
    public const NAME = 'model';

    /**
     * @param SimpleXMLElement[] $rawParameters
     */
    public function create(FeedContainerInterface $container, Feed $feed, $rawParameters): void
    {
        $rawProducts = $rawParameters[self::SOURCE_PRODUCTS]->xpath(self::SOURCE_PRODUCT);

        usort(
            $rawProducts,
            static function (SimpleXMLElement $a, SimpleXMLElement $b) {
                $aName = mb_strtolower(trim((string) $a->{self::NAME}));
                $bName = mb_strtolower(trim((string) $b->{self::NAME}));

                if ($aName === $bName) {
                    return 0;
                }

                return $aName > $bName ? -1 : 1;
            }
        );

        $container->setRawProducts($rawProducts);
    }
}
