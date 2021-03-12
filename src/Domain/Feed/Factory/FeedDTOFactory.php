<?php

namespace App\Domain\Feed\Factory;

use App\Domain\Feed\TransferObject\FeedDTO;
use App\Module\DataTransferObjectFactory\DataTransferObjectFactory;
use App\Module\Feed\FeedChecker;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

final class FeedDTOFactory
{
    private DataTransferObjectFactory $dataTransferObjectFactory;
    private FeedChecker $feedChecker;

    public function __construct(DataTransferObjectFactory $dataTransferObjectFactory, FeedChecker $feedChecker)
    {
        $this->dataTransferObjectFactory = $dataTransferObjectFactory;
        $this->feedChecker = $feedChecker;
    }

    public function createFromRequest(Request $request): FeedDTO
    {
        $dto = new FeedDTO();

        try {
            if ($request->request->has('url')) {
                $dto->setUrl($request->request->get('url', null));

                $feedType = $this->feedChecker->checkUrl($dto->getUrl());

                $dto->setType($feedType);
            }
        } catch (Throwable $exception) {
            //skip
        }

        if ($request->request->has('removedDescription')) {
            $dto->setRemovedDescription($request->request->get('removedDescription', null));
        }

        if ($request->request->has('stopWords')) {
            $dto->setStopWords((array) $request->request->get('stopWords', null));
        }

        if ($request->request->has('addedCity')) {
            $dto->setAddedCity($request->request->get('addedCity', null));
        }

        if ($request->request->has('name')) {
         $dto->setName($request->request->get('name', null));
        }

        if ($request->request->has('textAfterDescription')) {
            $dto->setTextAfterDescription($request->request->get('textAfterDescription', null));
        }

        if ($request->request->has('removeLastImage')) {
            $this->dataTransferObjectFactory->resolveBoolean(
                $dto,
                $request->request->getBoolean('removeLastImage'),
                static function (FeedDTO $feedDTO, bool $isTrue): void {
                    $isTrue ? $feedDTO->markAsRemoveLastImage() : $feedDTO->unmarkAsRemoveLastImage();
                }
            );
        }

        if ($request->request->has('excludeOutOfStockItems')) {
            $this->dataTransferObjectFactory->resolveBoolean(
                $dto,
                $request->request->getBoolean('excludeOutOfStockItems'),
                static function (FeedDTO $feedDTO, bool $isTrue): void {
                    $isTrue ? $feedDTO->markAsExcludeOutOfStockItems() : $feedDTO->unmarkAsExcludeOutOfStockItems();
                }
            );
        }

        return $dto;
    }
}
