<?php

namespace Tests\Acceptance\Controller\Rest;

use ByJG\ApiTools\AbstractRequester;
use ByJG\ApiTools\Base\Schema;
use ByJG\ApiTools\Exception\DefinitionNotFoundException;
use ByJG\ApiTools\Exception\GenericSwaggerException;
use ByJG\ApiTools\Exception\HttpMethodNotFoundException;
use ByJG\ApiTools\Exception\InvalidDefinitionException;
use ByJG\ApiTools\Exception\InvalidRequestException;
use ByJG\ApiTools\Exception\NotMatchedException;
use ByJG\ApiTools\Exception\PathNotFoundException;
use ByJG\ApiTools\Exception\StatusCodeNotMatchedException;
use ByJG\Util\Psr7\MessageException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class SwaggerApiTestCase extends WebTestCase
{
    use FixturesTrait;

    protected ?Schema $schema;

    protected function setUp(): void
    {
        parent::setUp();

        self::$kernel = static::bootKernel([]);

        $schema = Schema::getInstance(file_get_contents(self::$kernel->getCacheDir().'/../swagger.json'));
        $this->setSchema($schema);
    }

    protected function tearDown(): void
    {
        $this->schema = null;

        parent::tearDown();
    }

    public function setSchema($schema): void
    {
        $this->schema = $schema;
    }

    /**
     * @return mixed
     *
     * @throws DefinitionNotFoundException
     * @throws GenericSwaggerException
     * @throws HttpMethodNotFoundException
     * @throws InvalidDefinitionException
     * @throws NotMatchedException
     * @throws PathNotFoundException
     * @throws StatusCodeNotMatchedException
     * @throws InvalidRequestException
     * @throws MessageException
     */
    public function assertRequest(AbstractRequester $request)
    {
        // Add own schema if nothing is passed.
        if (!$request->hasSchema()) {
            $this->checkSchema();
            $request->withSchema($this->schema);
        }

        // Request based on the Swagger Request definitions
        $body = $request->send();

        // Note:
        // This code is only reached if the send is successful and
        // all matches are satisfied. Otherwise an error is threw before
        // reach this
        self::assertTrue(true);

        return $body;
    }

    /**
     * @throws GenericSwaggerException
     */
    protected function checkSchema(): void
    {
        if (!$this->schema) {
            throw new GenericSwaggerException('You have to configure a schema for either the request or the testcase');
        }
    }
}
