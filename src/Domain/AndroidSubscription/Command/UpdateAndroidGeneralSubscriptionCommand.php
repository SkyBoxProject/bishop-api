<?php

namespace AppBundle\Domain\AndroidSubscription\Command;

use AppBundle\Entity\User;

final class UpdateAndroidGeneralSubscriptionCommand
{
    private $user;
    private $isTest = false;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isTest(): bool
    {
        return $this->isTest;
    }

    public function markAsTest(): void
    {
        $this->isTest = true;
    }
}
