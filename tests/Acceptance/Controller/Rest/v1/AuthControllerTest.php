<?php

namespace App\Tests\Acceptance\Controller\Rest\v1;

use App\Domain\User\Entity\User;
use App\Domain\User\Exception\UserNotFound;
use App\Domain\User\Repository\UserRepository;
use App\Tests\Acceptance\Controller\Rest\SwaggerApiTestCase;
use App\Tests\DataFixtures\ORM\LoadUserFixtures;
use AppBundle\Entity\UserProgress;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\UserProgress\LoadUserProgressWithLikes;

final class AuthControllerTest extends SwaggerApiTestCase
{
    public function testRegisterNewUserMustBeSuccess(): void
    {
        $userRepository = $this->getContainer()->get(UserRepository::class);

        $email = 'email@test.com';
        $password = 'test';

        try {
            $userRepository->getByEmail($email);
        } catch (UserNotFound $exception) {
            self::assertTrue(true);
        }

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

        self::assertCount(3, $content);
        self::assertEquals(Response::HTTP_OK, $content['code']);
        self::assertEquals('Success', $content['message']);

        self::assertCount(3, $content['response']);
        self::assertArrayHasKey('token', $content['response']);
        self::assertArrayHasKey('refreshToken', $content['response']);
        self::assertArrayHasKey('tokenExpires', $content['response']);

        try {
            $userRepository->getByEmail($email);
        } catch (UserNotFound $exception) {
            self::assertTrue(false);
        }
    }

    public function testRegisterWithInvalidEmailMustBeError(): void
    {
        $email = 'emailtest.com';
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

        self::assertCount(2, $content);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $content['code']);
        self::assertEquals('Email invalid!', $content['message']);
    }

    public function testRegisterWithExistEmailMustBeError(): void
    {
        $referenceRepository = $this->loadFixtures([
            LoadUserFixtures::class,
        ])->getReferenceRepository();

        /** @var User $user */
        $user = $referenceRepository->getReference(LoadUserFixtures::REFERENCE_NAME);

        $email = $user->getEmail();
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

        self::assertCount(2, $content);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $content['code']);
        self::assertEquals('Email already exists.', $content['message']);
    }

    public function testRegisterWithEmptyParametersMustBeError(): void
    {
        $requester = $this->getRequester();
        $requester
            ->assertResponseCode(200)
            ->withQuery()
            ->withMethod('POST')
            ->withPath('/register')
            ->withRequestBody([
                'email' => '',
                'password' => '',
            ]);
        $response = $this->assertRequest($requester);

        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(2, $content);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $content['code']);
        self::assertEquals('Email or password not be empty.', $content['message']);
    }

    public function testLoginWithExistEmailMustBeSuccess(): void
    {
        $referenceRepository = $this->loadFixtures([
            LoadUserFixtures::class,
        ])->getReferenceRepository();

        /** @var User $user */
        $user = $referenceRepository->getReference(LoadUserFixtures::REFERENCE_NAME);

        $requester = $this->getRequester();
        $requester
            ->assertResponseCode(200)
            ->withQuery()
            ->withMethod('POST')
            ->withPath('/login')
            ->withRequestBody([
                'email' => $user->getEmail(),
                'password' => LoadUserFixtures::PLAIN_PASSWORD,
            ]);
        $response = $this->assertRequest($requester);

        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(3, $content);
        self::assertEquals(Response::HTTP_OK, $content['code']);
        self::assertEquals('Success', $content['message']);

        self::assertCount(3, $content['response']);
        self::assertArrayHasKey('token', $content['response']);
        self::assertArrayHasKey('refreshToken', $content['response']);
        self::assertArrayHasKey('tokenExpires', $content['response']);
    }

    public function testLoginWithNotExistEmailMustBeError(): void
    {
        $email = 'email@test.com';
        $password = 'test';

        $requester = $this->getRequester();
        $requester
            ->assertResponseCode(200)
            ->withQuery()
            ->withMethod('POST')
            ->withPath('/login')
            ->withRequestBody([
                'email' => $email,
                'password' => $password,
            ]);
        $response = $this->assertRequest($requester);

        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(2, $content);

        self::assertEquals(Response::HTTP_NOT_FOUND, $content['code']);
        self::assertEquals('User not found.', $content['message']);
    }

    public function testLoginWithWrongPasswordMustBeError(): void
    {
        $referenceRepository = $this->loadFixtures([
            LoadUserFixtures::class,
        ])->getReferenceRepository();

        /** @var User $user */
        $user = $referenceRepository->getReference(LoadUserFixtures::REFERENCE_NAME);

        $email = $user->getEmail();
        $password = 'test';

        $requester = $this->getRequester();
        $requester
            ->assertResponseCode(200)
            ->withQuery()
            ->withMethod('POST')
            ->withPath('/login')
            ->withRequestBody([
                'email' => $email,
                'password' => $password,
            ]);
        $response = $this->assertRequest($requester);

        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(2, $content);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $content['code']);
        self::assertEquals('User password is wrong!', $content['message']);
    }

    public function testTokenCheckWithExistTokenMustBeSuccess(): void
    {
        $token = $this->createTokenFromUser();

        $requester = $this->getRequester();
        $requester
            ->assertResponseCode(Response::HTTP_OK)
            ->withQuery()
            ->withMethod('GET')
            ->withPath('/token/check')
            ->withRequestHeader([
                'Authorization' => $token,
                'HTTP_Authorization' => $token,
            ]);

        $response = $this->assertRequest($requester);

        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(3, $content);
        self::assertEquals(Response::HTTP_OK, $content['code']);
        self::assertEquals('Success', $content['message']);
        self::assertTrue($content['isValid']);
    }

    public function testTokenCheckWithNotExistTokenMustBeError(): void
    {
        $requester = $this->getRequester();
        $requester
            ->assertResponseCode(Response::HTTP_UNAUTHORIZED)
            ->withQuery()
            ->withMethod('GET')
            ->withPath('/token/check')
            ->withRequestHeader([
                'Authorization' => 'token',
            ]);

        $response = $this->assertRequest($requester);

        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(2, $content);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $content['code']);
        self::assertEquals('Authorization required!', $content['message']);
    }

    public function testTokenRefreshWithInvalidRefreshTokenMustBeError(): void
    {
        $requester = $this->getRequester();
        $requester
            ->assertResponseCode(Response::HTTP_UNAUTHORIZED)
            ->withQuery()
            ->withMethod('POST')
            ->withPath('/token/refresh')
            ->withRequestBody([
                'refresh_token' => '123',
            ]);

        $response = $this->assertRequest($requester);

        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(2, $content);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $content['code']);
        self::assertEquals('Authorization required!', $content['message']);
    }

    public function testTokenRefreshWithRefreshTokenMustBeSuccess(): void
    {
        $referenceRepository = $this->loadFixtures([
            LoadUserFixtures::class,
        ])->getReferenceRepository();

        /** @var User $user */
        $user = $referenceRepository->getReference(LoadUserFixtures::REFERENCE_NAME);

        $requester = $this->getRequester();
        $requester
            ->assertResponseCode(200)
            ->withQuery()
            ->withMethod('POST')
            ->withPath('/login')
            ->withRequestBody([
                'email' => $user->getEmail(),
                'password' => LoadUserFixtures::PLAIN_PASSWORD,
            ]);
        $response = $this->assertRequest($requester);

        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        $requester = $this->getRequester();
        $requester
            ->assertResponseCode(200)
            ->withQuery()
            ->withMethod('POST')
            ->withPath('/token/refresh')
            ->withRequestBody([
                'refresh_token' => $content['response']['refreshToken'],
            ]);
        $response = $this->assertRequest($requester);

        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(3, $content);
        self::assertEquals(Response::HTTP_OK, $content['code']);
        self::assertEquals('Success', $content['message']);

        self::assertCount(3, $content['response']);
        self::assertArrayHasKey('token', $content['response']);
        self::assertArrayHasKey('refreshToken', $content['response']);
        self::assertArrayHasKey('tokenExpires', $content['response']);
    }

    public function createTokenFromUser(): string
    {
        $referenceRepository = $this->loadFixtures([
            LoadUserFixtures::class,
        ])->getReferenceRepository();

        /** @var User $user */
        $user = $referenceRepository->getReference(LoadUserFixtures::REFERENCE_NAME);

        return $this->createToken($user);
    }
}
