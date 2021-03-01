<?php

namespace App\Module\Feed\Common;

use App\Domain\Feed\Entity\Feed;
use App\Domain\Feed\Entity\ValueObject\FeedType;
use App\Module\Feed\Common\Container\FeedContainerInterface;
use App\Module\Feed\Common\Module\ModuleInterface;
use App\Module\Feed\Common\Product\ProductInterface;

interface FeedManagerInterface
{
    /**
     * @return ModuleInterface[]
     */
    public function getModules(): array;

    public function createEmptyProduct(): ProductInterface;

    public function build(Feed $feed): FeedContainerInterface;

    public static function getSupportType(): FeedType;
}
