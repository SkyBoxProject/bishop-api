<?php

namespace App\Domain\Feed\Query;

use App\Domain\User\Entity\User;

final class GetFeedByUserQuery
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
