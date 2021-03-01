<?php

namespace App\Domain\Feed\Query\Handler;

use App\Domain\Feed\Entity\Feed;
use App\Domain\Feed\Query\GetFeedByUuidQuery;
use App\Domain\Feed\Repository\FeedRepository;

final class GetFeedByUuidHandler
{
    private FeedRepository $feedRepository;

    public function __construct(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    public function __invoke(GetFeedByUuidQuery $query): Feed
    {
        return $this->feedRepository->getByUuid($query->getUuid());
    }
}
