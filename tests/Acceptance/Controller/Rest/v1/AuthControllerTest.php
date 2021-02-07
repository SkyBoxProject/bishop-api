<?php

namespace App\Tests\Acceptance\Controller\Rest\v1;

use App\Tests\Acceptance\Controller\Rest\SwaggerApiTestCase;
use ByJG\ApiTools\Base\Schema;
use Symfony\Component\HttpFoundation\Response;

final class AuthControllerTest extends SwaggerApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $schema = Schema::getInstance(file_get_contents('var/cache/swagger.json'));
        $this->setSchema($schema);
    }

    public function testRegisterNewUser(): void
    {
        $email = 'email@test.com';
        $password = 'test';

        $requester = $this->getRequester();
        $requester
            ->assertResponseCode(200)
            ->withQuery()
            ->withMethod('POST')
            ->withPath('/register')
            ->withRequestBody([
                'email' => $email,
                'password' => $password,
            ]);
        $response = $this->assertRequest($requester);

        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(Response::HTTP_OK, $content['code']);
        self::assertEquals('Success', $content['message']);

        self::assertCount(2, $content['response']);
        self::assertArrayHasKey('token', $content['response']);
        self::assertArrayHasKey('refreshToken', $content['response']);
    }
}
