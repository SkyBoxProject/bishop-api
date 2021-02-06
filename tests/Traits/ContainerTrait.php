<?php

namespace Tests\Traits;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerTrait
{
    private ?ContainerInterface $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }
}
