<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class TestCase extends KernelTestCase
{
    use FixturesTrait;

    final protected function getFaker(): Generator
    {
        return Factory::create();
    }

    final protected function getValidator(): ValidatorInterface
    {
        return $this->getContainer()->get('validator');
    }

    final protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
