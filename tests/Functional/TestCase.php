<?php

namespace Tests\Functional;

use App\Kernel;
use Doctrine\ORM\EntityManager;
use Faker\Factory;
use Faker\Generator;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\Traits\FileSystemTrait;

abstract class TestCase extends WebTestCase
{
    use FileSystemTrait;
    use FixturesTrait;

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        static::$kernel = null;
    }

    protected function getKernel(): KernelInterface
    {
        return static::$kernel;
    }

    final protected function getFaker(): Generator
    {
        return Factory::create();
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->getKernel()->getContainer();
    }

    protected function getEntityManager(): EntityManager
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->getContainer()->get('event_dispatcher');
    }
}
