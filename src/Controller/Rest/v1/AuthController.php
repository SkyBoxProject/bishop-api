<?php

namespace App\Controller\Rest\v1;

use App\Domain\EmailVerificationToken\Command\VerifyEmailCommand;
use App\Domain\User\Command\CreateUserCommand;
use App\Domain\User\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * @Route(condition="request.attributes.get('version') == 'v1'")
 */
final class AuthController extends AbstractFOSRestController
{
    private JWTTokenManagerInterface $authManager;
    private UserRepository $userRepository;
    private ContractsEventDispatcherInterface $dispatcher;
    private TranslatorInterface $translator;

    public function __construct(
        JWTTokenManagerInterface $authManager,
        UserRepository $userRepository,
        ContractsEventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator
    ) {
        $this->authManager = $authManager;
        $this->userRepository = $userRepository;
        $this->dispatcher = $eventDispatcher;
        $this->translator = $translator;
    }

    /**
     * @Operation(
     *     tags={"Авторизация"},
     *     summary="Вход и получение токена доступа",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             required={"email", "password"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Возвращается, когда успех",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=200, type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="response", ref="#/components/schemas/tokenInfo")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Возвращается, когда email или пароль пустые",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=400),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     *
     * @Route("/login", methods={"POST"})
     */
    public function login(Request $request): JsonResponse
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');

        if (empty($email) || empty($password)) {
            return new JsonResponse([
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => $this->translator->trans('Email or password not be empty.', [], 'error'),
            ]);
        }

        try {
            $user = $this->userRepository->getByEmailAndPassword($email, $password);
        } catch (Throwable $exception) {
            throw new UsernameNotFoundException($this->translator->trans('User not found.', [], 'error'));
        }

        $token = $this->authManager->create($user);

        $event = new AuthenticationSuccessEvent(['token' => $token], $user, new JsonResponse());

        $this->dispatcher->dispatch($event, Events::AUTHENTICATION_SUCCESS);

        $tokenTTL = $this->getParameter('lexik_jwt_authentication.token_ttl');

        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'message' => $this->translator->trans('Success', [], 'message'),
            'response' => [
                'token' => $event->getData()['token'],
                'refreshToken' => $event->getData()['refresh_token'],
                'tokenExpires' => (new DateTime())->setTimezone(new DateTimeZone('UTC'))->modify(sprintf('%s seconds', $tokenTTL))->format(DateTimeInterface::ATOM),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    /**
     * @Operation(
     *     tags={"Авторизация"},
     *     summary="Регистрация и получение токена доступа",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             required={"email", "password"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Возвращается, когда успех",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=200, type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="response", ref="#/components/schemas/tokenInfo")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Возвращается, когда email или пароль пустые",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=400),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     *
     * @Rest\Post("/register")
     */
    public function register(Request $request): JsonResponse
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');

        if (empty($email) || empty($password)) {
            return new JsonResponse([
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => $this->translator->trans('Email or password not be empty.', [], 'error'),
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse([
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'message' => $this->translator->trans('Email invalid!', [], 'error'),
            ]);
        }

        $this->dispatchMessage(new CreateUserCommand($email, $password));

        return $this->login($request);
    }

    /**
     * @Operation(
     *     tags={"Авторизация"},
     *     summary="Обеовить токен доступа по токену обновления",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="refresh_token", type="string"),
     *             required={"refresh_token"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Возвращается, когда успех",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=200, type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="response", ref="#/components/schemas/tokenInfo")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Возвращается, когда ошибка",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=401, type="integer"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     *
     * @Rest\Post("/token/refresh")
     */
    public function refresh(Request $request, RefreshToken $refreshTokenService): JsonResponse
    {
        $responseData = (array) json_decode($refreshTokenService->refresh($request)->getContent());

        if (!isset($responseData['token'])) {
            return new JsonResponse([
                'code' => JsonResponse::HTTP_UNAUTHORIZED,
                'message' => $this->translator->trans('Authorization required!', [], 'error'),
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $tokenTTL = $this->getParameter('lexik_jwt_authentication.token_ttl');

        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'message' => $this->translator->trans('Success', [], 'message'),
            'response' => [
                'token' => $responseData['token'],
                'refreshToken' => $responseData['refresh_token'],
                'tokenExpires' => (new DateTime())->setTimezone(new DateTimeZone('UTC'))->modify(sprintf('%s seconds', $tokenTTL))->format(DateTimeInterface::ATOM),
            ],
        ]);
    }

    /**
     * @Operation(
     *     tags={"Авторизация"},
     *     summary="Подтверждение электронной почты",
     *     @OA\Response(
     *         response="302",
     *         description="Возвращается, когда успех"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Возвращается, когда ошибка"
     *     )
     * )
     *
     * @Route("/email-verification/{token}", methods={"GET"})
     */
    public function emailVerification(string $token): RedirectResponse
    {
        $indexPageUrl = sprintf('http://%s', $this->getParameter('web_domain'));
        $notFoundPageUrl = sprintf('http://%s/404', $this->getParameter('web_domain'));

        if (empty($token)) {
            $this->redirect($notFoundPageUrl);
        }

        try {
            $this->dispatchMessage(new VerifyEmailCommand($token));
        } catch (Throwable $exception) {
            return $this->redirect($notFoundPageUrl);
        }

        return $this->redirect($indexPageUrl);
    }

    /**
     * @Operation(
     *     tags={"Авторизация"},
     *     summary="Проверка токена",
     *     @OA\Response(
     *         response="200",
     *         description="Возвращается, когда успех",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=200, type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="isValid", type="bool")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Возвращается, когда ошибка токена доступа",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=401, type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="isValid", type="bool")
     *         )
     *     )
     * )
     *
     * @Rest\Get("/token/check")
     */
    public function check(): JsonResponse
    {
        $user = $this->getUser();

        if ($user === null) {
            return new JsonResponse([
                'code' => JsonResponse::HTTP_UNAUTHORIZED,
                'message' => $this->translator->trans('Failed', [], 'message'),
                'isValid' => false,
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'message' => $this->translator->trans('Success', [], 'message'),
            'isValid' => true,
        ]);
    }
}
