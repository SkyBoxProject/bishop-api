<?php

declare(strict_types=1);

namespace App\Domain\Feed\Command\Handler;

use App\Domain\Feed\Command\UpdateFeedCommand;
use App\Domain\Feed\Entity\Feed;
use App\Domain\Feed\Repository\FeedRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateFeedHandler implements MessageHandlerInterface
{
    private FeedRepository $feedRepository;

    public function __construct(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    public function __invoke(UpdateFeedCommand $command): Feed
    {
        $feed = $command->getFeed();

        $feedDTO = $command->getFeedDTO();

        $feed->updateFromDTO($feedDTO);

        $this->feedRepository->save($feed);

        return $feed;
    }
}
