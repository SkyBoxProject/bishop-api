<?php

declare(strict_types=1);

namespace App\Domain\Feed\Command\Handler;

use App\Domain\Feed\Command\CreateFeedCommand;
use App\Domain\Feed\Entity\Feed;
use App\Domain\Feed\Repository\FeedRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Uid\UuidV4;

final class CreateFeedHandler implements MessageHandlerInterface
{
    private FeedRepository $feedRepository;

    public function __construct(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    public function __invoke(CreateFeedCommand $command): Feed
    {
        $feedDTO = $command->getFeedDTO();

        $feed = new Feed(UuidV4::v4(), $command->getUser(), $feedDTO->getUrl(), $feedDTO->getType());

        $feed->updateFromDTO($feedDTO);

        $this->feedRepository->save($feed);

        return $feed;
    }
}
