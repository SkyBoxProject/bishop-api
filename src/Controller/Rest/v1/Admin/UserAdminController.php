<?php

namespace App\Controller\Rest\v1\Admin;

use App\Domain\License\Normalizer\LicenseNormalizer;
use App\Domain\User\Entity\User;
use App\Domain\User\Query\GetUserByIdQuery;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * @Route(condition="request.attributes.get('version') == 'v1'")
 */
final class UserAdminController extends AbstractFOSRestController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Operation(
     *     tags={"Пользователь"},
     *     summary="Получить данные текущего пользователя",
     *     @OA\Response(
     *         response="200",
     *         description="Возвращается, когда успех",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", example=200, type="integer"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="response", type="object",
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     *
     * @Route("/user/{id}", methods={"GET"})
     */
    public function getUserInfo(string $id, LicenseNormalizer $licenseNormalizer): JsonResponse
    {
        try {
            /** @var User $user */
            $user = $this
                ->dispatchMessage(new GetUserByIdQuery(UuidV4::fromString($id)))
                ->last(HandledStamp::class)
                ->getResult();
        } catch (Throwable $exception) {
            return new JsonResponse([
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $this->translator->trans('User not found.', [], 'error'),
            ], JsonResponse::HTTP_OK);
        }

        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'message' => $this->translator->trans('Success', [], 'message'),
            'response' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'licenses' => $licenseNormalizer->normalizeCollection($user->getLicenses()),
            ],
        ]);
    }
}
