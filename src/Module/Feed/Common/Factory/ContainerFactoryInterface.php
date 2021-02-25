<?php

namespace App\Module\Feed\Common\Factory;

use App\Domain\Feed\Entity\Feed;
use App\Module\Feed\Common\Container\FeedContainerInterface;

interface ContainerFactoryInterface
{
    /**
     * @param mixed $rawParameters
     */
    public function create(FeedContainerInterface $container, Feed $feed, $rawParameters): void;
}
