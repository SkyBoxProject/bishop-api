<?php

namespace App\Module\Feed\Basic\Factory;

use App\Domain\Feed\Entity\Feed;
use App\Module\Feed\Basic\Container\BasicFeedContainer;
use App\Module\Feed\Common\Container\FeedContainerInterface;
use App\Module\Feed\Common\Factory\ContainerFactoryInterface;
use SimpleXMLElement;

final class CategoriesBasicFeedFactory implements ContainerFactoryInterface
{
    public const SOURCE_CATEGORIES = 'categories';
    public const SOURCE_CATEGORY = 'category';
    public const CATEGORY_ID = 'id';
    public const CATEGORY_PARENT_ID = 'parentId';
    public const CATEGORY_PARENT = 'parent';
    public const CATEGORY_NAME = 'name';

    /**
     * @param FeedContainerInterface|BasicFeedContainer $container
     * @param SimpleXMLElement[]                        $rawParameters
     */
    public function create(FeedContainerInterface $container, Feed $feed, $rawParameters): void
    {
        $categoriesXml = $rawParameters[self::SOURCE_CATEGORIES]->xpath(self::SOURCE_CATEGORY);
        $categories = [];

        foreach ($categoriesXml as $category) {
            $id = (int) $category->attributes()->{self::CATEGORY_ID}->__toString();
            $parentId = $category->attributes()->{self::CATEGORY_PARENT_ID};

            $categories[$id] = [
                self::CATEGORY_PARENT => $parentId ? (int) $parentId->__toString() : null,
                self::CATEGORY_NAME => (string) $category->__toString(),
            ];
        }

        $container->setCategories($categories);
    }
}
