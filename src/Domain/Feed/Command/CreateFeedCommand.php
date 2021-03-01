<?php

declare(strict_types=1);

namespace App\Domain\Feed\Command;

use App\Domain\Feed\TransferObject\FeedDTO;
use App\Domain\User\Entity\User;

final class CreateFeedCommand
{
    private FeedDTO $feedDTO;

    private User $user;

    public function __construct(User $user, FeedDTO $feedDTO)
    {
        $this->feedDTO = $feedDTO;
        $this->user = $user;
    }

    public function getFeedDTO(): FeedDTO
    {
        return $this->feedDTO;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
