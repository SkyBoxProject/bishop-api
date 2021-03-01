<?php

namespace App\Module\Feed\Basic\Module;

use App\Module\Feed\Basic\Container\BasicFeedContainer;
use App\Module\Feed\Common\Container\FeedContainerInterface;
use App\Module\Feed\Common\Module\ResolveCategoryModuleInterface;
use App\Module\Feed\Common\Product\ProductInterface;
use SimpleXMLElement;

final class ResolveCategoryBasicFeedModule implements ResolveCategoryModuleInterface
{
    public const CATEGORY_ID = 'category_id';
    public const CATEGORY_PARENT = 'parent';
    public const CATEGORY_NAME = 'name';

    /**
     * @param FeedContainerInterface|BasicFeedContainer $container
     * @param SimpleXMLElement $rawParameters
     */
    public function execute(FeedContainerInterface $container, ProductInterface $product, $rawProduct): void
    {
        //todo ДОДЕЛАТЬ!
        try {
            $category = $this->findCategory($container->getCategories(), (int) $rawProduct->{self::CATEGORY_ID});

            $category = str_replace(',', '', $category);

            $product->setCategory($category);
        } catch (\Throwable $exception) {
        }
    }

    private function findCategory(array $categories, int $id): string
    {
        $category = $categories[$id];

        $result = [$category[self::CATEGORY_NAME]];

        if ($category[self::CATEGORY_PARENT] !== null) {
            $result[] = $this->findCategory($categories, $category[self::CATEGORY_PARENT]);
        }

        $result = array_reverse($result);

        return implode('/', $result);
    }
}
