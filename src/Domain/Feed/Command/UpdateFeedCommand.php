<?php

declare(strict_types=1);

namespace App\Domain\Feed\Command;

use App\Domain\Feed\Entity\Feed;
use App\Domain\Feed\TransferObject\FeedDTO;

final class UpdateFeedCommand
{
    private Feed $feed;
    private FeedDTO $feedDTO;

    public function __construct(Feed $feed, FeedDTO $feedDTO)
    {
        $this->feedDTO = $feedDTO;
        $this->feed = $feed;
    }

    public function getFeed(): Feed
    {
        return $this->feed;
    }

    public function getFeedDTO(): FeedDTO
    {
        return $this->feedDTO;
    }
}
