<?php

namespace App\Controller\Rest\v1;

use App\Domain\Feed\Command\CreateFeedCommand;
use App\Domain\Feed\Command\UpdateFeedCommand;
use App\Domain\Feed\Entity\Feed;
use App\Domain\Feed\Entity\ValueObject\FeedType;
use App\Domain\Feed\Factory\FeedDTOFactory;
use App\Domain\Feed\Query\GetFeedByUserQuery;
use App\Domain\Feed\Query\GetFeedByUuidQuery;
use App\Domain\User\Entity\User;
use App\Module\Feed\FeedChecker;
use App\Module\Feed\FeedManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * @Route(condition="request.attributes.get('version') == 'v1'")
 */
final class FeedController extends AbstractFOSRestController
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * @Operation(
     *     tags={"Feed"},
     *     summary="Create feed",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string"),
     *             @OA\Property(property="removed_description", type="string"),
     *             @OA\Property(property="stop_words", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="added_city", type="string"),
     *             @OA\Property(property="text_after_description", type="string"),
     *             @OA\Property(property="is_remove_last_image", type="boolean"),
     *             @OA\Property(property="is_exclude_out_of_stock_items", type="boolean"),
     *             required={"url"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @Rest\Post("/feed")
     */
    public function createFeed(Request $request, FeedDTOFactory $feedDTOFactory): JsonResponse
    {
        $feedDTO = $feedDTOFactory->createFromRequest($request);

        /** @var Feed $feed */
        $feed = $this
            ->dispatchMessage(new CreateFeedCommand($this->getUser(), $feedDTO))
            ->last(HandledStamp::class)
            ->getResult();

        return new JsonResponse([
            'code' => JsonResponse::HTTP_CREATED,
            'id' => (string) $feed->getUuid(),
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Operation(
     *     tags={"Feed"},
     *     summary="Update feed",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="removed_description", type="string"),
     *             @OA\Property(property="stop_words", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="added_city", type="string"),
     *             @OA\Property(property="text_after_description", type="string"),
     *             @OA\Property(property="is_remove_last_image", type="boolean"),
     *             @OA\Property(property="is_exclude_out_of_stock_items", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @Rest\Put("/feed/{id}")
     */
    public function updateFeed(Request $request, string $id, FeedDTOFactory $feedDTOFactory): JsonResponse
    {
        $feedDTO = $feedDTOFactory->createFromRequest($request);

        try {
            /** @var Feed $feed */
            $feed = $this
                ->dispatchMessage(new GetFeedByUuidQuery(UuidV4::fromString($id)))
                ->last(HandledStamp::class)
                ->getResult();
        } catch (Throwable $exception) {
            return new JsonResponse([
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $this->translator->trans('Feed not found.', ['%uuid%' => $id], 'error'),
            ], JsonResponse::HTTP_OK);
        }

        $this
            ->dispatchMessage(new UpdateFeedCommand($feed, $feedDTO))
            ->last(HandledStamp::class)
            ->getResult();

        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'id' => (string) $feed->getUuid(),
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @Operation(
     *     tags={"Feed"},
     *     summary="Get feed",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @Rest\Get("/feed/{id}")
     */
    public function getFeed(string $id): JsonResponse
    {
        try {
            /** @var Feed $feed */
            $feed = $this
                ->dispatchMessage(new GetFeedByUuidQuery(UuidV4::fromString($id)))
                ->last(HandledStamp::class)
                ->getResult();
        } catch (Throwable $exception) {
            return new JsonResponse([
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $this->translator->trans('Feed not found.', ['%uuid%' => $id], 'error'),
            ], JsonResponse::HTTP_OK);
        }

        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'response' => $feed,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @Operation(
     *     tags={"Feed"},
     *     summary="Get feed",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @Rest\Get("/feed")
     */
    public function getFeeds(): JsonResponse
    {
        try {
            $feeds = $this
                ->dispatchMessage(new GetFeedByUserQuery($this->getUser()))
                ->last(HandledStamp::class)
                ->getResult();
        } catch (Throwable $exception) {
            return new JsonResponse([
                'code' => JsonResponse::HTTP_OK,
                'response' => [],
            ], JsonResponse::HTTP_OK);
        }

        $response = [];

        /** @var Feed $feed */
        foreach ($feeds as $feed) {
            $response[] = $feed->jsonSerialize();
        }

        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'response' => $response,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @Operation(
     *     tags={"Feed"},
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
     *         description="Returned when successful"
     *     )
     * )
     *
     * @Rest\Post("/feed/{id}/convert")
     */
    public function convert(string $id, FeedManager $feedManager)
    {
        try {
            /** @var Feed $feed */
            $feed = $this
                ->dispatchMessage(new GetFeedByUuidQuery(UuidV4::fromString($id)))
                ->last(HandledStamp::class)
                ->getResult();
        } catch (Throwable $exception) {
            return new JsonResponse([
                'code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $this->translator->trans('Feed not found.', ['%uuid%' => $id], 'error'),
            ], JsonResponse::HTTP_OK);
        }

        $stream = $feedManager->execute($feed);

        return new StreamedResponse(static function () use ($stream) {
            $outputStream = $stream;
        });
    }
}
