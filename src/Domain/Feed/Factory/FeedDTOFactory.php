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

        if ($request->request->has('removed_description')) {
            $dto->setRemovedDescription($request->request->get('removed_description', null));
        }

        if ($request->request->has('stop_words')) {
            $dto->setStopWords((array) $request->request->get('stop_words', null));
        }

        if ($request->request->has('added_city')) {
            $dto->setAddedCity($request->request->get('added_city', null));
        }

        if ($request->request->has('text_after_description')) {
            $dto->setTextAfterDescription($request->request->get('text_after_description', null));
        }

        if ($request->request->has('is_remove_last_image')) {
            $this->dataTransferObjectFactory->resolveBoolean(
                $dto,
                $request->request->getBoolean('is_remove_last_image'),
                static function (FeedDTO $feedDTO, bool $isTrue): void {
                    $isTrue ? $feedDTO->markAsRemoveLastImage() : $feedDTO->unmarkAsRemoveLastImage();
                }
            );
        }

        if ($request->request->has('is_exclude_out_of_stock_items')) {
            $this->dataTransferObjectFactory->resolveBoolean(
                $dto,
                $request->request->getBoolean('is_exclude_out_of_stock_items'),
                static function (FeedDTO $feedDTO, bool $isTrue): void {
                    $isTrue ? $feedDTO->markAsExcludeOutOfStockItems() : $feedDTO->unmarkAsExcludeOutOfStockItems();
                }
            );
        }

        return $dto;
    }
}
