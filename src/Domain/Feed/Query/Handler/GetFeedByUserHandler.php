<?php

namespace App\Domain\Feed\Query\Handler;

use App\Domain\Feed\Query\GetFeedByUserQuery;
use App\Domain\Feed\Repository\FeedRepository;

final class GetFeedByUserHandler
{
    private FeedRepository $feedRepository;

    public function __construct(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    public function __invoke(GetFeedByUserQuery $query): array
    {
        return $this->feedRepository->getByUser($query->getUser());
    }
}
