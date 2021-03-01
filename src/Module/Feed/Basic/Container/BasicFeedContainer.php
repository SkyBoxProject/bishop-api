<?php

namespace App\Module\Feed\Basic\Container;

use App\Domain\Feed\Entity\Feed;
use App\Module\Feed\Common\Container\FeedContainerInterface;
use SimpleXMLElement;

final class BasicFeedContainer implements FeedContainerInterface
{
    private Feed $feed;

    /** @var SimpleXMLElement[] */
    private $rawProducts;

    private $categories;

    /**
     * @param SimpleXMLElement[] $rawProducts
     */
    public function __construct(Feed $feed, $rawProducts)
    {
        $this->feed = $feed;
        $this->rawProducts = $rawProducts;
    }

    public function getFeed(): Feed
    {
        return $this->feed;
    }

    /**
     * @return SimpleXMLElement[]
     */
    public function getRawProducts(): array
    {
        return $this->rawProducts;
    }

    /**
     * @param SimpleXMLElement[] $rawProducts
     */
    public function setRawProducts($rawProducts): self
    {
        $this->rawProducts = $rawProducts;

        return $this;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories($categories): self
    {
        $this->categories = $categories;

        return $this;
    }
}
