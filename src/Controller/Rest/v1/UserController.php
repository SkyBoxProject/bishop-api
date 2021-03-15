<?php

namespace App\Controller\Rest\v1;

use App\Domain\License\Normalizer\LicenseNormalizer;
use App\Domain\User\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(condition="request.attributes.get('version') == 'v1'")
 */
final class UserController extends AbstractApiController
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
     * @Route("/user", methods={"GET"})
     */
    public function getUserInfo(LicenseNormalizer $licenseNormalizer): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'message' => $this->translator->trans('Success', [], 'message'),
            'response' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'licenses' => $licenseNormalizer->normalizeCollectionForUser($user->getLicenses()),
            ],
        ]);
    }
}
