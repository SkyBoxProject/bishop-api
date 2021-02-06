<?php

namespace AppBundle\Domain\AndroidSubscription\Query;

use AppBundle\Entity\User;

final class GetAndroidSubscriptionWithMaxExpirationTimeQuery
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
