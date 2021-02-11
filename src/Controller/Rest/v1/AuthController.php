<?php

namespace App\Controller\Rest\v1;

use App\Domain\User\Command\CreateUserCommand;
use App\Domain\User\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     *     tags={"Auth"},
     *     summary="User authentication",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             required={"email", "password"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=200, type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="response", ref="#/components/schemas/tokenInfo")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returned when email or password empty",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=400),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     *
     * @Rest\Post("/login")
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

        $user = $this->userRepository->getByEmailAndPassword($email, $password);

        $token = $this->authManager->create($user);

        $event = new AuthenticationSuccessEvent(['token' => $token], $user, new JsonResponse());

        $this->dispatcher->dispatch($event, Events::AUTHENTICATION_SUCCESS);

        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'message' => $this->translator->trans('Success', [], 'message'),
            'response' => [
                'token' => $event->getData()['token'],
                'refreshToken' => $event->getData()['refresh_token'],
            ],
        ]);
    }

    /**
     * @Operation(
     *     tags={"Auth"},
     *     summary="User registration",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             required={"email", "password"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=200, type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="response", ref="#/components/schemas/tokenInfo")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returned when email or password empty",
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

        $this->dispatchMessage(new CreateUserCommand($email, $password));

        return $this->login($request);
    }

    /**
     * @Operation(
     *     tags={"Auth"},
     *     summary="Token refresh",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="refresh_token", type="string"),
     *             required={"refresh_token"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=200, type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="response", ref="#/components/schemas/tokenInfo")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Returned when successful",
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

        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'message' => $this->translator->trans('Success', [], 'message'),
            'response' => [
                'token' => $responseData['token'],
                'refreshToken' => $responseData['refresh_token'],
            ],
        ]);
    }

    /**
     * @Operation(
     *     tags={"Auth"},
     *     summary="Token check",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=200, type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="isValid", type="bool")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Returned when wrong token",
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
