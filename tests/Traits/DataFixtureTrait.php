<?php

namespace App\Tests\Traits;

trait DataFixtureTrait
{
    protected function getDataFixturesFolder(): string
    {
        return __DIR__.'/../DataFixtures/';
    }

    protected function getOutputFolder(): string
    {
        return __DIR__.'/../Output/';
    }
}
