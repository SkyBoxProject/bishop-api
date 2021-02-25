<?php

namespace App\Module\Feed\Basic;

use App\Domain\Feed\Entity\Feed;
use App\Domain\Feed\Entity\ValueObject\FeedType;
use App\Module\Feed\Basic\Container\BasicFeedContainer;
use App\Module\Feed\Common\Factory\ContainerFactoryInterface;
use App\Module\Feed\Common\FeedManagerInterface;
use App\Module\Feed\Common\Module\ModuleInterface;
use App\Module\Feed\Common\Product\Product;
use App\Module\Feed\Common\Product\ProductInterface;
use SimpleXMLElement;

final class BasicFeedManager implements FeedManagerInterface
{
    private array $factories;
    private array $modules;
    private BasicFeedContainer $container;

    /**
     * @param ContainerFactoryInterface[] $factories
     * @param ModuleInterface[]           $modules
     */
    public function __construct(array $factories, array $modules)
    {
        $this->factories = $factories;
        $this->modules = $modules;

        $this->container = new BasicFeedContainer();
    }

    /**
     * @return ModuleInterface[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    public function getContainer(): BasicFeedContainer
    {
        return $this->container;
    }

    public function createEmptyProduct(): ProductInterface
    {
        return new Product();
    }

    public static function getSupportType(): FeedType
    {
        return FeedType::basic();
    }

    public function build(Feed $feed): self
    {
        $rawParameters = $this->getRawParameters($feed->getUrl());

        foreach ($this->factories as $factory) {
            $factory->create($this->container, $feed, $rawParameters);
        }

        return $this;
    }

    /**
     * @return SimpleXMLElement[]
     */
    private function getRawParameters(string $url): array
    {
        $xmlString = file_get_contents($url);

        return (array) simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
    }
}
