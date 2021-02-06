<?php

namespace AppBundle\Domain\AndroidSubscription\Query\Handler;

use AppBundle\Domain\AndroidSubscription\Entity\AndroidSubscription;
use AppBundle\Domain\AndroidSubscription\Exception\AndroidSubscriptionNotFoundException;
use AppBundle\Domain\AndroidSubscription\Query\GetAndroidSubscriptionWithMaxExpirationTimeQuery;
use AppBundle\Domain\AndroidSubscription\Repository\AndroidSubscriptionRepository;

final class GetAndroidSubscriptionWithMaxExpirationTimeHandler
{
    private $androidSubscriptionRepository;

    public function __construct(AndroidSubscriptionRepository $androidSubscriptionRepository)
    {
        $this->androidSubscriptionRepository = $androidSubscriptionRepository;
    }

    public function handle(GetAndroidSubscriptionWithMaxExpirationTimeQuery $query): AndroidSubscription
    {
        $subscription = $this->androidSubscriptionRepository->findAndroidSubscriptionWithMaxExpirationTime($query->getUser());

        if ($subscription === null) {
            throw new AndroidSubscriptionNotFoundException();
        }

        return $subscription;
    }
}
