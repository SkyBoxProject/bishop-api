<?php

namespace App\Module\Feed\Common\Container;

use App\Domain\Feed\Entity\Feed;

interface FeedContainerInterface
{
    public function getFeed(): Feed;

    /**
     * @return mixed
     */
    public function getRawProducts();

    /**
     * @param mixed $rawProducts
     */
    public function setRawProducts($rawProducts): self;
}
